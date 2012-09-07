<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Comments_parser {

    public $parents  = array();
    public $children = array();

    /**
     * @param array $comments
     */
    public function arrange($comments)
    {
        foreach ($comments as $comment)
        {
            if ($comment['parent_id'] === NULL || $comment['parent_id'] == 0)
            {
                $this->parents[$comment['id']][] = $comment;
            }
            else
            {
                $this->children[$comment['parent_id']][] = $comment;
            }
        }
        $this->print_comments();
    }

    private function tabulate($depth)
    {
        for ($depth; $depth > 0; $depth--)
        {
            //echo "t";
        }
    }

    /**
     * @param array $comment
     * @param int $depth
     */
    private function format_comment($comment, $depth)
    {
        $this->tabulate($depth+1);

        echo "<li>";
        echo $comment['comment'];
        echo "</li>";
    }

    /**
     * @param array $comment
     * @param int $depth
     */
    private function print_parent($comment, $depth = 0)
    {
        $this->tabulate($depth);
        echo "<ul>";
        foreach ($comment as $c)
        {
            $this->format_comment($c, $depth);

            if (isset($this->children[$c['id']]))
            {
                $this->print_parent($this->children[$c['id']], $depth + 1);
            }
        }
        $this->tabulate($depth);
        echo "</ul>";
    }

    private function print_comments()
    {
        foreach ($this->parents as $c)
        {
            $this->print_parent($c);
        }
    }

}