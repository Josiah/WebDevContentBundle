(function($,document,window){
    var keys = {
        ESC: 27
    };
    
    // Onload Configuration
    $(function($){    
        $('[data-content-block]').each(function(){
            var $block = $(this);
            var blockHtml = $block.html();
            var url = $block.attr('data-content-uri');
            
            $block.css({'position': 'relative'});
            
            var $edit = $(document.createElement('button'))
                .appendTo($block)
                .text("Edit")
                .attr({'class': 'btn btn-mini'})
                .css({
                    'position': 'absolute',
                    'right': 0,
                    'top': 0
                })
            ;
            
            $edit.click(function(event){
                event.preventDefault();
                var editorID = 'content-editor-'+$block.attr('data-content-block');
                
                var $shade = $(document.createElement('div'))
                    .appendTo('body')
                    .css({
                        'position': 'fixed',
                        'left': 0,
                        'top': 0,
                        'width': '100%',
                        'height': '100%',
                        'background-color': '#000',
                        'z-index': 1000
                    })
                    .fadeTo(200,0.5)
                ;
                
                var $modal = $(document.createElement('div'))
                    .appendTo('body')
                    .addClass('modal')
                ;
                
                var $modalBody = $(document.createElement('div'))
                    .appendTo($modal)
                    .attr('id',editorID)
                    .addClass('modal-body');
                
                var $modalFooter = $(document.createElement('div'))
                    .appendTo($modal)
                    .addClass('modal-footer');
                
                var $saveButton = $(document.createElement('button'))
                    .appendTo($modalFooter)
                    .text('Save')
                    .addClass('btn').addClass('btn-primary').addClass('pull-right');
                
                $(document).on('keyup',function(event){
                    if (event.keyCode == keys.ESC) {
                        $shade.remove();
                        $modal.remove();
                    }
                });
                
                var editor = CKEDITOR.appendTo(editorID,{
                    toolbar: [
                        {name: 'clipboard', items: ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo']},
                        {name: 'basicstyles', items: ['Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat']},
                        {name: 'paragraph', items: ['NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote',
                                                    '-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock']},
                        {name: 'links', items : ['Link','Unlink','Anchor']},
                        {name: 'insert', items : ['Table','HorizontalRule','SpecialChar']},
                        {name: 'styles', items : ['Styles','Format','Font','FontSize']},
                        {name: 'tools', items : ['ShowBlocks','-','About']}
                    ]
                },blockHtml);
                
                $saveButton.click(function(event){
                    event.preventDefault();
                    
                    if (editor.checkDirty()) {
                        $.ajax(url, {
                            type: "POST",
                            data: {'content': editor.getData()},
                            dataType: 'json',
                            success: function(data){
                                window.location.reload();
                            }
                        });
                    }
                });
            });
        });
    });
})(jQuery,document,window);