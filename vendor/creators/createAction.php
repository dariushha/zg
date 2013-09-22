<?php
namespace zinux\zg\vendor\creators;
/**
 * Description of createmoduleBootstrap
 *
 * @author dariush
 */

class createAction extends \zinux\zg\operators\baseOperator
{
    public function __construct(\zinux\zg\vendor\item $controller, \zinux\zg\vendor\item $action, $project_path = ".")
    {
        $ns = $this->convert_to_relative_path($controller->path, $project_path);
        $action->path = preg_replace("#(\w+)action$#i","$1", $action->name)."Action";
        $this ->cout("Creating new action '",0.5,  self::defColor, 0)
                ->cout($action->path, 0, self::yellow, 0)
                ->cout("' in '",0,self::defColor, 0)
                ->cout("$ns\\{$controller->name}", 0, self::yellow, 0)
                ->cout("'.");
        $mbc = "
    /**
    * The \\{$controller->parent->parent->name}\\{$controller->parent->name}\\controllers\\{$controller->name}::{$action->path}()
    * @by Zinux Generator <b.g.dariush@gmail.com>
    */
    public function {$action->path}()
    {
        
    }
";
        
        if(!\zinux\kernel\utilities\fileSystem::resolve_path("{$controller->path}"))
            throw new \zinux\kernel\exceptions\notFoundException("{$controller->name} not found ...");
        
        $this->check_php_syntax($controller->path);
        
        require_once $controller->path;
        
        
        $s = $this->GetStatus($project_path);
        
        $class = "$ns\\{$controller->name}";
        
        if(!class_exists($class))
            throw new \zinux\kernel\exceptions\notFoundException("Class {$controller->name} not found ....");
    
        
        $rf = new \ReflectionClass($class);
        
        if(!$rf->isSubclassOf('\zinux\kernel\controller\baseController'))
            throw new \zinux\kernel\exceptions\invalideOperationException("'$class' should be a sub class of '\zinux\kernel\controller\baseController'");
        if(!method_exists(new $class, $action->path))
        {
            $n = new \zinux\zg\vendor\reflections\ReflectionClass($class);
            $n->AddMethod($mbc);
        }
        
        $action->parent = $controller;
        $s->modules->collection[strtolower($controller->parent->name)]->controller[strtolower($controller->name)]->action[strtolower($action->name)] = $action;
        $this->SaveStatus($s);
        
    }
}
?>
