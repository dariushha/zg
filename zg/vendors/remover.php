<?php
namespace zg\vendors;
/**
 * Zinux item remotion handler
 */
class remover extends \zg\operators\baseOperator
{
    /**
     * ctor a new remover
     */
    public function __construct()
    {
        parent::__construct(1);
    }
    /**
     * removes items from file system
     * @param \zg\vendors\item $item target item to remove
     * @param boolean $rebuild should re-build the project after remotion
     * @param string $project_path project directory
     */
    public function removeFS(item $item, $rebuild = 1,$project_path = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($project_path,1);
        # get status object
        $s = $this->GetStatus($project_path);
        # remove the item from file system 
        exec("rm -fr '{$item->path}'");
        # indicate the success
        $this->cout("- {$item->path}", 0.5, self::getColor(self::red));
        # if no rebuid? return
        if(!$rebuild) return;
        # invoke a rebuilder
        $b = new \zg\operators\build(1, 0);
        $this ->cout("$ Updating index file.....", 0.5, self::defColor, 0);
        # rebuild the config file
        $b->build(array('-p', $s->project->path, "-m", \str_replace($s->project->path, "", $s->modules->meta->path), "-a", @$s->project->meta->app_path ? $s->project->meta->app_path : "application"));
        $this->cout(" [ DONE ]", 0, self::green);
    } 
   /**
    * removes an action from its parent
    * @param \zg\vendors\item $action target action to remove
    * @param string $project_path project directory
    */
    public function removeAction(item $action,$project_path = ".")
    {
        # this opt is valid under project directories
        $this->CheckZG($project_path,1);
        # invoke an action remover
        new \zg\vendors\removers\removeAction($action, $project_path);
    }
}