<?php
namespace zg\parser;
/**
 * This class parsers help content of opts
 */
class helpParser extends baseParser
{    
    /**
     * Runs the help parser
     */
    public function Run()
    {
        # a stack for parsing
        $stack = array(array("", $this->command_generator->Generate()));
        # check if mode head lines only is enabled
        $head_lines = $this->remove_arg($this->args, "--heads");
        # unflag the verboseness
        $verbose = 0;
        #if any arg supplied 
        if(count($this->args))
        {
            $args = $this->args;
            # invoke a creator based on current args
            $n = new parser($args, $this->command_generator);
            # get the help content acoording to passed arg
            $stack = array(array("", $n->getOperator()));
            # enable verboseness mode
            $verbose = 1;
        }
        # if we are in a head lines only mode
        if($head_lines)
        {
            # fetch parser keywords
            $kw = unserialize(PARSER_KEYWORDS);
            $this->cout("Valid operation list for '".self::getColor(self::yellow)."zg ".implode(" ", $this->args).self::getColor(self::defColor)."': ");
            $found = 0;
            # foreach item in first elem of stack
            foreach($stack[0][1] as $key=> $value)
            {
                # skip it if it is a keyword
                if(array_key_exists($key, $kw))continue;
                $found = 1;
                # print it
                $this->cout("> ", 0.5, self::getColor(self::yellow), 0)->cout($key, 0, self::getColor(self::cyan));
            }
            # if no headlines found
            if(!$found)
            {
                # indicate it
                $this->cout("'".self::getColor(self::yellow)."zg ".implode(" ", $this->args).self::getColor(self::defColor)."' is a solo operation!", 0.5)
                        ->cout("No sub-operation found!",1, self::getColor(self::red));
            }
            return;
        }
        # foreach stach item
        while(count($stack))
        {
            # pop from stack
            $value = array_pop($stack);
            $key = $value[0];
            $value = $value[1];
            # if it is an mixed value
            if(!isset($value->title))
            {
                # skip it if not iterable 
                if(!$this->is_iterable($value)) continue;
                $tmps = array();
                # foreach sub-item in current item
                foreach($value as $_key => $sub_value)
                {
                    # push it into stack
                    array_push($tmps, array($_key, $sub_value));
                }
                # thread stack as queue
                while(count($tmps))
                    array_push($stack, array_pop($tmps));
            }
            else
            {
                # if it is a printable item
                 $tmps = array();
                # foreach sub-item in current item
                foreach($value as $_key => $sub_value)
                {
                    # skip it if not iterable 
                    if(!$this->is_iterable($value)) continue;
                    # if it is still a mixed item
                    if($key!="instance" && $key!="help")
                        # push it to the stack
                        array_push($tmps, array($_key, $sub_value));
                }
                # thread stack as queue
                while(count($tmps))
                    array_push($stack, array_pop($tmps));
                # print current item's help content
                $this->printHelp($value, $verbose);
            }
        }
        # indicate notes
        $this->cout()->cout(">    Type 'zg -h \$command' to print more help about that command.", 0,self::getColor(self::hiBlue));
        $this->cout()->cout(">    Type 'zg -h (\$command) --head' to print headline operations.", 0,self::getColor(self::hiBlue));
    }
    /**
     * This print help content for passed content
     * @param \stdClass $content content that contains standard defined property to print its help
     * @param boolean $render_options check if it should print its option property too
     */
    protected function printHelp($content, $render_options = 0)
    {
        # if its not match with help content standard return
        if(!(isset($content->title) && isset($content->help))) return;
        /**
         * print general help content
         */
        $command = preg_replace("#(\\\$\w+)#i", self::getColor(self::defColor).self::getColor(self::yellow)."$1".self::getColor(self::hiYellow), $content->help->command);
        $rep_pat = "$1".str_repeat(" ", 3*5);
        $this ->cout()
                ->cout($content->title, 1, self::getColor(self::cyan))
                ->cout(self::getColor(self::hiYellow).preg_replace(array("#(\n)#i", "#(<br\s*(/)?>)#i"), array($rep_pat, $rep_pat),  $command), 2, self::getColor(self::defColor));
        # if has an alias print it
        if(isset($content->help->alias))
            $this->cout("Alias: [ ".self::getColor(self::hiYellow).preg_replace("#(\\\$\w+)#i", self::getColor(self::defColor).self::getColor(self::yellow)."$1".self::getColor(self::hiYellow), $content->help->alias).self::getColor(self::defColor)." ]", 3, self::getColor(self::defColor));
        $this->cout();
        $rep_pat = "$1".str_repeat(" ", 3*5);
        $this ->cout(preg_replace(array("#(\n)#i", "#(<br\s*(/)?>)#i"), array($rep_pat, $rep_pat),  $content->help->detail), 3);
        /**
         * prints options if told so and exists so
         */
        if($render_options && isset($content->options))
        {
            $this->cout()->cout("Options: ", 2, self::getColor(self::hiYellow));
            $rep_pat = "$1".str_repeat(" ", 3*6);
            foreach($content->options as $option => $exp)
            {
                $this ->cout()
                        ->cout($option, 3, self::getColor(self::yellow), 0)
                        ->cout(" : ", 0, self::getColor(self::defColor), 0)
                        ->cout(preg_replace(array("#(\n)#i", "#(<br\s*(/)?>)#i"), array($rep_pat, $rep_pat), $exp), 0, self::getColor(self::yellow));
            }
        }
        /**
         * if contains any default value
         */
        if(isset($content->defaults))
        {
            # print it
            $this ->cout()->cout("Default Values:", 2, self::getColor(self::hiYellow))->cout();
            $rep_pat = "$1".str_repeat(" ", 3*6);
            foreach($content->defaults as $arg => $value)
            {
                $this ->cout($arg, 3, self::getColor(self::yellow), 0)
                        ->cout(" : ", 0, self::getColor(self::defColor), 0)
                        ->cout(preg_replace(array("#(\n)#i", "#(<br\s*(/)?>)#i"), array($rep_pat, $rep_pat), $value), 0, self::getColor(self::yellow));
            }
        }
        /**
         * In case of redering options and existance of notes for current item
         */
        if($render_options && isset($content->notes))
        {
            # print it
            $this ->cout()->cout("Notes:", 2, self::getColor(self::hiYellow));
            $rep_pat = "$1".str_repeat(" ", 3*6);
            foreach($content->notes as $index => $note)
            {
                $index++;
                $this ->cout()
                        ->cout("$index ) ", 3, self::getColor(self::yellow), 0)
                        ->cout(preg_replace(array("#(\n)#i", "#(<br\s*(/)?>)#i"), array($rep_pat, $rep_pat), $note))
                        ->cout();
            }
        }
    }
}