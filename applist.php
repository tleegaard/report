<?php global $path; ?>

<script type="text/javascript" src="<?php echo $path; ?>Modules/report/table.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>Modules/report/applist.js"></script>

<style>
  #table td {
    border: 0;
    font-size:14px; 
    padding:2px 8px 2px 8px;
  }

  #table th {
    padding-top:20px;
    border: 0;
    border-bottom: 1px solid #DDD;
  }

  #table tr.trtotal td {
    font-weight:bold;
    font-size:16px;
    padding:25px 8px 25px 8px;
  }

  #table tr.additem td {
    padding:8px;
    background-color:#eee;
  }

  #table tbody {
    border: 0;
  }

  input[type=text] {
    height:25px;
    margin-bottom:0px;
    width:80%;
  }

  #table td:nth-of-type(2) { width:80px; text-align: center; }
  #table td:nth-of-type(3) { width:80px; text-align: center; }
  #table td:nth-of-type(4) { width:80px; text-align: center; }
  #table td:nth-of-type(5) { width:30px; text-align: center; }

  #table th:nth-of-type(2) { width:80px; text-align: center; }
  #table th:nth-of-type(3) { width:80px; text-align: center; }
  #table th:nth-of-type(4) { width:80px; text-align: center; }
  #table th:nth-of-type(5) { width:30px; text-align: center; }
  #table th:nth-of-type(6) { width:30px; text-align: center; }
</style>


<div class="container">
    <h2><?php echo _('Appliance list'); ?></h2>
    <p>Explore electricity consumption further by building a simple appliance list model of all the appliances that make up the monitored electricity consumption above. Try to match both pie charts so that the average total kWh/d from the monitored data and the model data match and the kWh/d consumed at different powers also match.</p>

    
<!--
    <ul class="nav nav-tabs">
      <li class="active"><a href="#" id="baseline">Baseline</a></li>
      <li><a href="#" id="target">Target</a></li>
    </ul>
-->

    <div class="row">
      <div class="span7"><div id="table"></div></div>
      <div class="span5" style="background-color:#eee"><div id="applistpie" style="width:100%; height:400px;"></div></div>
    </div>

    <div id="nofeeds" class="alert alert-block hide">
        <h4 class="alert-heading">No feeds created</h4>
        <p>Feeds are where your monitoring data is stored. The recommended route for creating feeds is to start by creating inputs (see the inputs tab). Once you have inputs you can either log them straight to feeds or if you want you can add various levels of input processing to your inputs to create things like daily average data or to calibrate inputs before storage.</p>
    </div>

    <button id="addItemButton" class="btn btn-primary" style="margin-top:-70px; margin-left:8px;">Add new item</button>
</div>

<div id="AppListModal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="AppListModalLabel" aria-hidden="true" data-backdrop="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="AppListModalLabel">Add item</h3>
  </div>
  <div id="AppListModalBody" class="modal-body">

  <p>Describe the item you would like to add below, if you dont know the power consumption and estimated hours that its on for but know the watt hours consumed in a day instead use the watt hours box</p>

  <p><b>Category:</b><br>
  <input type='text' id='itemcategory' style="width:200px"  /></p>
  <p><b>Descriptive name: (unique within category)</b><br>
  <input type='text' id='itemname' style="width:200px" /></p>
  <p><b>Power (Watts):</b><br>
  <input type='text' id='itempower' style="width:200px" /></p>
  <p><b>Hours (0-24):</b><br>
  <input type='text' id='itemhours' style="width:200px" /></p>
  <p><b>Watt-hours:</b><br>
  <input type='text' id='itemwatthours' style="width:200px" /></p>

  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
    <button id="addsave-button" class="btn btn-primary">Add</button>
  </div>
</div>

<script>

  var path = "<?php echo $path; ?>";

  table.element = "#table";

  table.fields = {
    'name':{'title':"", 'type':"text"},
    'power':{'title':"<?php echo _('Power'); ?>", 'type':"text"},
    'hours':{'title':"<?php echo _('Hours'); ?>", 'type':"text"},
    'watthours':{'title':"<?php echo _('Watt-hours'); ?>", 'type':"text"},

    // Actions
    'edit-action':{'title':'', 'type':"edit"},
    'delete-action':{'title':'', 'type':"delete"}
  }

  table.groupby = 'category';
  table.deletedata = false;

  var baseline = [
    {'category':"Lighting", 'name':"Light Incandecent", 'power':40, 'hours':6},
    {'category':"Lighting", 'name':"Light CFL", 'power':11, 'hours':6},
    {'category':"Lighting", 'name':"Light LED (Pharox)", 'power':6, 'hours':6},

    {'category':"Electronics", 'name':"Laptop", 'power':25,'hours':8},
    {'category':"Electronics", 'name':"Phone charger", 'power':4.3,'hours':6},

    {'category':"Appliances", 'name':"Immersion heater", 'power':3200,'hours':1.0},
    {'category':"Appliances", 'name':"Kettle", 'power':2600,'hours':0.4}

  ];

  /*
  var target = [
    {'category':"Lighting", 'name':"Light LED", 'power':6, 'hours':6},
    {'category':"Lighting", 'name':"Light LED", 'power':6, 'hours':6},
    {'category':"Lighting", 'name':"Light LED", 'power':6, 'hours':6},

    {'category':"Electronics", 'name':"Laptop", 'power':25,'hours':8},
    {'category':"Electronics", 'name':"Phone charger", 'power':4.3,'hours':6},
   
    {'category':"Appliances", 'name':"Immersion heater", 'power':3200,'hours':0.5},
    {'category':"Appliances", 'name':"Kettle", 'power':2600,'hours':0.2}
  ];*/

  // listid = 0 will create a new list
  var listid = 0;
  var listname = '';

  var lists = applist.getlists();

  console.log("Applists:");
  console.log(lists);
  if (!lists.length) {
    console.log("No lists");
    listid = 0;
    listname = 'baseline';
  } else {
    listid = lists[0].id;
    listname = lists[0].name;
    table.data = applist.get(listid);
  }

  if (!table.data) table.data = baseline;

  table.draw();
  draw_pie(table.energyrange);

  $("#table").bind("onEdit", function(e){ });

  $("#table").bind("onSave", function(e,id,fields_to_update){ 
    listid = applist.set(listid,listname,table.data);
    console.log("Listid: "+listid);
    draw_pie(table.energyrange);
  });

  $("#table").bind("onDelete", function(e,id,row){
    table.remove(row);
    listid = applist.set(listid,listname,table.data);
    console.log("Listid: "+listid);
    draw_pie(table.energyrange);
  });

  $("#addsave-button").click(function() {
    $('#AppListModal').modal('hide');

    table.data.push({
      'category':$("#itemcategory").val(), 
      'name':$("#itemname").val(), 
      'power':$("#itempower").val(),
      'hours':$("#itemhours").val(),
      'watthours':$("#itemwatthours").val()
    });

    table.draw();
    draw_pie(table.energyrange);
    listid = applist.set(listid,listname,table.data);
    console.log("Listid: "+listid);
  });

  $("#addItemButton").click(function() {
    $('#AppListModal').modal('show');
  });

/*
  $("#target").click(function() {
    baseline = table.data;
    table.data = target;
    table.draw();
    draw_pie(table.energyrange);
    $("#baseline").parent().removeClass('active');
    $("#target").parent().addClass('active');
  });

  $("#baseline").click(function() {
    target = table.data;
    table.data = baseline;
    table.draw(); 
    draw_pie(table.energyrange);
    $("#baseline").parent().addClass('active');
    $("#target").parent().removeClass('active');
  });
*/

  function draw_pie(range)
  {
    var applistpiedata = [
      {label: "Less than 1 kW", color: "#4da74d",data: range[0]},  
      {label: "1 kW to 3 kW", color: "#edc240",data: range[1]}, 
      {label: "More than 3 kW", color: "#cb4b4b",data: range[2]}
    ];

		$.plot("#applistpie", applistpiedata, {
			series: {
				pie: { 
					show: true,
          radius: 2/3,
				  label: { formatter: applistlabelFormatter },
          stroke: { width:3 }
				}
			},
      legend: { show: false },
      grid: {
          hoverable: true,
          clickable: true
      }
		});
  }

	function applistlabelFormatter(label, thisseries) {
    var applist_prependUnits = '';
    var applist_appendUnits = ' kWh';
		return "<div style='font-size:11pt; text-align:center; padding:2px; color:#444; width:150px'>" + label+": <b>"+Math.round(thisseries.percent) + "%</b><br/ >" + applist_prependUnits+(thisseries.data[0][1]/1000.0).toFixed(2) +applist_appendUnits+"/d</div>";
	}

</script>
