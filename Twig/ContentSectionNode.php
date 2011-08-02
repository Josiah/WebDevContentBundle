<?php namespace WebDev\ContentBundle\Twig;

use Twig_Compiler as Compiler;
use Twig_Node as Node;
use Twig_Node_Expression as Expression;

class ContentSectionNode
    extends Node
{
    public function __construct(
        $name,
        $placeholder,
        $lineno,
        $tag = null)
    {
        parent::__construct(compact('placeholder'), compact('name'), $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param \Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        $content = "\$this->env->getExtension('content')";
        $name = $this->getAttribute('name');

        // Inject the placeholder as the content if the content is empty
        $compiler
            ->write("if(!{$content}->has('{$name}'))".PHP_EOL)
            ->write("{".PHP_EOL)
            ->indent()
                ->write("ob_start();".PHP_EOL)
                ->subcompile($this->getNode('placeholder'))
                ->write("{$content}->add('{$name}', ob_get_contents());".PHP_EOL)
                ->write("ob_end_clean();".PHP_EOL)
            ->outdent()
            ->write("}".PHP_EOL);
        
        // Output the content
        $compiler
            ->write("echo \$this->env->getExtension('content')->render('{$name}');".PHP_EOL);
    }
}