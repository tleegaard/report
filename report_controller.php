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

    require "Modules/report/report_model.php"; 
    $report = new Report($mysqli);

    if ($route->format == 'html' && $session['write'])
    {
      if ($route->action == 'view') $result = view("Modules/report/view.php", array());
    }

    if ($route->format == 'json' && $session['write'])
    {
      if ($route->action == 'setsettings') $result = $report->set_settings($session['userid'],get('settings'));
      if ($route->action == 'getsettings') $result = $report->get_settings($session['userid']);
    }

    return array('content'=>$result);
  }

?>
