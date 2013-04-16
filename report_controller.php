<?php
  /*
   All Emoncms code is released under the GNU Affero General Public License.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org

  */

  // no direct access
  defined('EMONCMS_EXEC') or die('Restricted access');

  function report_controller()
  {
    global $mysqli, $session, $route;

    $result = false;

    require "Modules/report/report_model.php"; 
    $report = new Report($mysqli);

    require "Modules/report/applist_model.php"; 
    $applist = new AppList($mysqli);

    if ($route->format == 'html' && $session['write'])
    {
      if ($route->action == 'electric') $result = view("Modules/report/electric.php", array());
      //if ($route->action == 'applist') $result = view("Modules/report/applist.php", array());
    }

    if ($route->format == 'json' && $session['write'])
    {
      if ($route->action == 'setsettings') $result = $report->set_settings($session['userid'],get('settings'));
      if ($route->action == 'getsettings') $result = $report->get_settings($session['userid']);

      if ($route->action == 'getlists') $result = $applist->get_lists($session['userid']);
      if ($route->action == 'getapplist') $result = $applist->get($session['userid'],get('id'));
      if ($route->action == 'setapplist') $result = $applist->set($session['userid'],get('id'),get('name'),get('list'));
      if ($route->action == 'deleteapplist') $result = $applist->delete($session['userid'],get('id'));
    }

    return array('content'=>$result);
  }

?>
