<?php namespace WebDev\ContentBundle\Twig;

use Twig_TokenParser as TokenParser;
use Twig_Token as Token;
use Twig_Node_Expression_Array as ArrayExpression;

class ContentSectionTokenParser
    extends TokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param Twig_Token $token A Twig_Token instance
     * @return Twig_NodeInterface A Twig_NodeInterface instance
     */
    public function parse(Token $token)
    {
        $stream = $this->parser->getStream();
        $name = $stream->expect(Token::NAME_TYPE)->getValue();

        // Placeholder Content
        if($stream->test(Token::BLOCK_END_TYPE))
        {
            $stream->next();
            $placeholder = $this->parser->subparse(array($this, 'detectEndBlock'), true);
        }

        // Close the parser
        $stream->expect(Token::BLOCK_END_TYPE);

        return new ContentSectionNode($name, $placeholder, $token->getLine(), $this->getTag());
    }

    public function detectEndBlock(Token $token)
    {
        return $token->test('endsection') || $token->test('end');
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'section';
    }
}