<?php

/**
 * @category    SxCore
 * @package     Html
 */
namespace SxCore\Html;

use SxCore\Html\Exception;

class HtmlElement
{

    /**
     * The tag to render
     *
     * @var string
     */
    protected $tag;

    /**
     * The children of the tag
     *
     * @var array
     */
    protected $children = array();

    /**
     * The attributes of the tag
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * The content of the tag
     *
     * @var string
     */
    protected $content = '';

    /**
     * Append, or prepend set content next to rendered children
     *
     * @var     string  prepend|append
     */
    protected $contentConcat = 'prepend';

    /**
     * Contruct tag
     *
     * @param   string  $tag    Tagname (Allowed tags are html tags, div, span, a etc)
     */
    public function __construct($tag = 'div')
    {
        $this->tag = $tag;
    }

    /**
     * Set position of content to append
     *
     * @return  \SxCore\Html\HtmlElement
     */
    public function setAppendContent()
    {
        $this->contentConcat = 'append';

        return $this;
    }

    /**
     * Set position of content to prepend
     *
     * @return  \SxCore\Html\HtmlElement
     */
    public function setPrependContent()
    {
        $this->contentConcat = 'prepend';

        return $this;
    }

    /**
     * Set tag attributes
     *
     * @param   array   $attributes
     *
     * @return  \SxCore\Html\HtmlElement
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Add tag attributes
     *
     * @param   array   $attributes
     *
     * @return  \SxCore\Html\HtmlElement
     */
    public function addAttributes(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     *
     * @param   string  $key
     * @param   string  $value
     *
     * @return  \SxCore\Html\HtmlElement
     *
     * @throws  \SxCore\Html\Exception\InvalidArgumentException
     */
    public function addAttribute($key, $value)
    {
        if (!is_string($key) || ((!is_string($value)) && !is_numeric($value))) {
            throw new Exception\InvalidArgumentException(
                'Invalid key or value type supplied. Expected string.'
            );
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return HtmlElement
     */
    public function removeAttribute($key)
    {
        if (!empty($this->attributes[$key])) {
            unset($this->attributes[$key]);
        }

        return $this;
    }

    /**
     * Get tag attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Add class to tag
     *
     * @param   string  $class
     *
     * @return  \SxCore\Html\HtmlElement
     */
    public function addClass($class)
    {
        if (!empty($this->attributes['class'])) {
            $class = $this->attributes['class'] . " $class";
        }

        return $this->addAttribute('class', $class);
    }

    /**
     * Set the content. (Overwrites old content)
     *
     * @param   string  $content
     *
     * @return  \SxCore\Html\HtmlElement
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Append content before other content
     *
     * @param   string  $content
     *
     * @return  \SxCore\Html\HtmlElement
     */
    public function appendContent($content)
    {
        $this->content .= $content;

        return $this;
    }

    /**
     * Prepend content before other content
     *
     * @param   string  $content
     *
     * @return  \SxCore\Html\HtmlElement
     */
    public function prependContent($content)
    {
        $this->content = $content . $this->content;

        return $this;
    }

    /**
     * Remove content
     *
     * @return \SxCore\Html\HtmlElement
     */
    public function removeContent()
    {
        $this->content = '';

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Spawn child
     *
     * @param   string  $tag
     *
     * @return  \SxCore\Html\HtmlElement
     */
    public function spawnChild($tag = null)
    {
        return $this->children[] = new self($tag);
    }

    /**
     * Add child to tag
     *
     * @param   \SxCore\Html\HtmlElement   $child
     *
     * @return  \SxCore\Html\HtmlElement
     */
    public function addChild(self $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Add children to tag
     *
     * @param   array   $children
     *
     * @return  \SxCore\Html\HtmlElement
     */
    public function addChildren(array $children)
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }

        return $this;
    }

    /**
     * Remove children
     *
     * @return  \SxCore\Html\HtmlElement
     */
    public function removeChildren()
    {
        $this->children = array();

        return $this;
    }

    /**
     * Set children
     *
     * @param   array   $children
     *
     * @return  \SxCore\Html\HtmlElement
     */
    public function setChildren(array $children)
    {
        $this->removeChildren();

        return $this->addChildren($children);
    }

    /**
     * Check for children
     *
     * @return  bool
     */
    public function hasChildren()
    {
        return !empty($this->children);
    }

    /**
     * Render content
     *
     * @return  string
     */
    public function render()
    {
        $content = '';

        if ($this->hasChildren()) {
            $content = $this->renderChildren($this->children);
        }

        if ('append' === $this->contentConcat) {
            $content .= $this->getContent();
        } else {
            $content = $this->getContent() . $content;
        }

        return $this->getTag($content);
    }

    /**
     * Render tag
     *
     * @param   string  $content
     *
     * @return  string
     */
    protected function getTag($content)
    {
        $attributes = $this->renderAttributes();

        return sprintf(
            '<%1$s%2$s>%3$s</%1$s>',
            $this->tag,
            $attributes,
            $content
        );
    }

    /**
     * Render tag attributes
     *
     * @return  string
     */
    protected function renderAttributes()
    {
        $attributes = '';

        foreach ($this->attributes as $key => $value) {
            $attributes .= " $key=\"$value\"";
        }

        return $attributes;
    }

    /**
     * @param array|HtmlElement[] $children
     *
     * @return string
     */
    protected function renderChildren(array $children)
    {
        $content = '';

        foreach ($children as $child) {
            $content .= $child->render();
        }

        return $content;
    }

    /**
     * Output tag
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

}
