<?php

namespace Nahid\Literature;

abstract class Scratch
{
    protected $_author = null;
    protected $_name = null;
    protected $words = [];

    public function __construct($name, $heading = 'h2')
    {
        $this->_name = $this->_makeTagable($name, $heading);
        $this->words[] = [];
    }

    public function __call($method, $args)
    {
        $tag = null;

        if (count($args) > 0) {
            $tag = $args[0];
        }

        return $this->_makeWord($method, $tag);
    }

    protected function _makeWord($word, $tag = null)
    {
        end($this->words);
        $last = key($this->words);

        $word = $this->_makeTagable($word, $tag);
        $line = $this->words[$last];
        array_push($line, $word);

        $this->words[$last] = $line;

        return $this;
    }

    protected function _makeTagable($word, $tag = null)
    {
        if (!is_null($tag)) {
            $tag = strtolower($tag);
            $word = '<' . $tag . '>' . $word . '</' . $tag . '>';
        }

        return $word;
    }

    public function author($name, $label = 'Author', $tag = 'h3')
    {
        $this->_author = $this->_makeTagable('<' . $tag . '>' . $label . ': ' . $name . '</' . $tag . '>');
    }

    public function _($word = null, $tag = null)
    {
        $specialChar = [
            '.', ',', null
        ];

        if (!is_null($word)) {
            $this->_makeWord($word, $tag);
        }

        if (in_array($word, $specialChar)) {
            $this->words[] = [];

        }

        return $this;
    }

    public function publish()
    {
        $body = '';

        $body .= $this->_name;
        $body .= $this->_author;

        foreach ($this->words as $words) {
            foreach ($words as $word) {
                $body .= $word . ' ';
            }

            if(count($words) > 1) {
                $body .= '</br>';
            }
        }

        echo $body;
    }

    public function para(callable $fn, $tag = 'p')
    {
        $tags = explode(' ', $tag);

        foreach ($tags as $tag) {
            $this->_makeWord('<' . $tag . '>')->_();
        }

        $fn($this);

        krsort($tags);
        foreach ($tags as $tag) {
            $this->_makeWord('</' . $tag . '>')->_();
        }

        return $this;
    }
}