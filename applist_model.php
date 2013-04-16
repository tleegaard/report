<?php

  /*
   All Emoncms code is released under the GNU Affero General Public License.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org

  */

class AppList
{

  private $mysqli;

  public function __construct($mysqli)
  {
    $this->mysqli = $mysqli;
  }

  public function get_lists($userid)
  {
    $userid = intval($userid);
    $result = $this->mysqli->query("SELECT id,name FROM applist WHERE `userid` = '$userid'");

    $data = array();
    while ($row = $result->fetch_object()) $data[] = $row;
    return $data;
  }

  public function set($userid,$id,$name,$list)
  {
    $id = intval($id);
    $userid = intval($userid);
    $name = preg_replace('/[^\w\s-]/','',$name);
    $array = json_decode($list);
    $output = array();

    // Sanitise input
    foreach ($array as $item) 
    {
      $itemout = array();
      if (isset($item->category)) $itemout['category'] = preg_replace('/[^\w\s-]/','',$item->category);
      if (isset($item->name)) $itemout['name'] = preg_replace('/[^\w\s-]/','',$item->name);
      if (isset($item->power)) $itemout['power'] = floatval($item->power);
      if (isset($item->hours)) $itemout['hours'] = floatval($item->hours);
      if ($itemout) $output[] = $itemout;
    }

    $list = json_encode($output);

    $row = false;
    if ($id>0) {
      $result = $this->mysqli->query("SELECT * FROM applist WHERE `userid` = '$userid' AND `id` = '$id'");
      $row = $result->fetch_array();
    }

    if ($row) {
      $result = $this->mysqli->query("UPDATE applist SET `list`='$list', `name`='$name' WHERE `userid`='$userid' AND `id` = '$id'");
    } else {
      $result = $this->mysqli->query("INSERT INTO applist (userid,name,list) VALUES ('$userid','$name','$list')");
      $id = $this->mysqli->insert_id;
    }

    return $id;   
  }

  public function get($userid,$id)
  {
    $userid = intval($userid);
    $result = $this->mysqli->query("SELECT name,list FROM applist WHERE `userid` = '$userid' AND `id` = '$id'");
    $row = $result->fetch_array();
    return json_decode($row['list']);
  }

  public function delete($userid,$id)
  {
    $userid = intval($userid);
    $result = $this->mysqli->query("DELETE FROM applist WHERE `userid` = '$userid' AND `id` = '$id'");
  }
}
