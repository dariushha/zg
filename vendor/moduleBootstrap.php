<?php
namespace zinux\zg\vendor;
/**
 * Description of createmoduleBootstrap
 *
 * @author dariush
 */
class moduleBootstrap extends \zinux\zg\baseZg
{
    public function __construct(Item $module, Item $moduleBootstrap, $project_path = ".")
    {
        $moduleBootstrap->name = preg_replace("#bootstrap$#i","", $moduleBootstrap->name)."Bootstrap";
        $this ->cout("Creating new module '", 1,  self::defColor, 0)
                ->cout($moduleBootstrap->name, 0, self::yellow, 0)
                ->cout("' at '",0,self::defColor, 0)
                ->cout(dirname($moduleBootstrap->path), 0, self::yellow, 0)
                ->cout("'.");
        $ns = preg_replace(
            array("#^".DIRECTORY_SEPARATOR."#i","#(\w+)(".DIRECTORY_SEPARATOR.")#i"),
            array("", "$1\\"), 
            str_replace(dirname($module->parent->path), "", dirname($moduleBootstrap->path))
        );
        $mbc = "<?php
namespace $ns;
/**
* The {$module->name}'s Bootstrapper
*/
class {$moduleBootstrap->name}
{
    /**
     * A pre-dispatch function
     * @param \\zinux\\kernel\\routing\\request \$request
     */
    public function pre_CHECK(\\zinux\\kernel\\routing\\request \$request)
    {
    }
    /**
     * A post-dispatch function
     * @param \\zinux\\kernel\\routing\\request \$request
     */
    public function post_CHECK(\\zinux\\kernel\\routing\\request \$request)
    {
    }
}";
        file_put_contents($moduleBootstrap->path, $mbc);
        $this->cout("+", 1, self::green,0);
        $s = $this->GetStatus($project_path);
        $moduleBootstrap->parent = $module;
        $s->modules->modules[$module->name]->bootstrap = $moduleBootstrap;
        $this->SaveStatus($s);
        $this->cout("+", 0, self::green);
    }
}

?>