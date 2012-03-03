<?php
/**
 * Application initialization plugin
 * 
 * @uses Zend_Controller_Plugin_Abstract
 * @todo Examine control struct
 */
class Plugin_Db_Profiler extends Zend_Controller_Plugin_Abstract
{
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        
        $profiler = $bootstrap->getResource('db')->getProfiler();
        
        if ($profiler->getEnabled() !== true) {
            return;
        }
        
        $msecs    = $profiler->getTotalElapsedSecs();
        $nqueries = $profiler->getTotalNumQueries();
        
        $longestTime = $longestQuery = 0;
        
        $view = Zend_Layout::getMvcInstance()->getView();
        
        $view->placeholder('profiler')->setPrefix('<div id="profiler">')->setPostfix('</div>');
        
        if (false === ($profiles = $profiler->getQueryProfiles())) {
            return;
        }
        
        $count = 1;
        $sqlData = array();
        foreach ($profiles as $query) {
            if ($query->getElapsedSecs() > $longestTime) {
                $longestTime  = $query->getElapsedSecs();
                $longestQuery = $count;
            }
            $sqlData[] = array(
                'count' => $count,
                'query' => $query->getQuery(),
                'seconds' => $query->getElapsedSecs()
            );
            $count++;
        }
        
        $data = array(
            'queries' => $sqlData,
            'elapsedMilliSeconds' => $msecs,
            'averageQueryTime' => ($msecs / $nqueries),
            'queriesPerSecond' => ($nqueries / $msecs),
            'longestQueryTime' => $longestTime,
            'longestQueryId' => $longestQuery
        );
        
        $view->placeholder('profiler')->append($view->partial('profiler.phtml', $data));
    }
}