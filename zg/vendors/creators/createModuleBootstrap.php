<?php
namespace zg\vendors\creators;
/**
 * module bootstrap creator
 */
class createModuleBootstrap extends \zg\baseZg
{
    /**
     * ctor a new module bootstrap
     * @param \zg\vendors\item $module target module item
     * @param \zg\vendors\item $moduleBootstrap target module bootstrap item
     * @param string $project_path project directory
     */
    public function __construct(\zg\vendors\item $module, \zg\vendors\item $moduleBootstrap, $project_path = ".")
    {
        $ns = $this->convert_to_relative_path($moduleBootstrap->path, $project_path);
        $this ->cout("Creating new module '", 0.5,  self::getColor(self::defColor), 0)
                ->cout($moduleBootstrap->name, 0, self::getColor(self::yellow), 0)
                ->cout("' at '",0,self::getColor(self::defColor), 0)
                ->cout($ns, 0, self::getColor(self::yellow), 0)
                ->cout("'.");
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
        
        $s = $this->GetStatus($project_path);
        $moduleBootstrap->parent = $module;
        $s->modules->collection[strtolower($module->name)]->bootstrap = $moduleBootstrap;
        $this->SaveStatus($s);
    }
}

?>
