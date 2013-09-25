<?php global $path; 

  /*
   All Emoncms code is released under the GNU Affero General Public License.
   See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org

  */

?>

<!--[if IE]><script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/excanvas.min.js"></script><![endif]-->
<script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/jquery.flot.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/jquery.flot.selection.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/jquery.flot.time.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/jquery.flot.stack.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path;?>Lib/flot/jquery.flot.pie.min.js"></script>

<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Modules/feed/feed.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Modules/report/report.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $path; ?>Modules/vis/visualisations/common/inst.js"></script>

<br><br>

<div class="hero-unit">

  <div class="btn-group" style="float:right ">
    <button id="options-button" class="btn btn-info" data-toggle="modal" data-target="#ModalConfigure"><i class="icon-wrench"></i> <?php echo _('Configure'); ?></button>   
  </div>

  <div class="btn-group" style="float:right; margin-right:10px;">
    <button class="btn time" time="365" ><?php echo _('Year'); ?></button>
    <button class="btn time" time="30" "><?php echo _('Month'); ?></button>
    <button class="btn time" time="14" "><?php echo _('2 Weeks'); ?></button>
    <button class="btn time" time="7" "><?php echo _('Week'); ?></button>
  </div>

  <div class="btn-group" style="float:right; margin-right:10px;">
    <button id="money" class="btn"><?php echo _('GBP'); ?></button>
    <button id="energy" class="btn"><?php echo _('kWh'); ?></button>
  </div>

  <h1><?php echo _('My Electric'); ?></h1>
  <br>
  <div>
    <div style="float:left;">
      <span style="font-size:32px; font-weight:bold; line-height:32px;"><span id="last"></span></span><br>
      <span style="font-size:32px; font-weight:bold; line-height:32px;"><span id="previous"></span></span>
    </div>
    <div style="float:left; padding-left:85px">
      <span style="font-size:64px; padding:0; margin:0; font-weight:bold; line-height:64px;"><span id="prc-change"><span></span>
    </div>

  <div style="clear:both;"></div>
  </div>
  <br>
  <div style="float:left; width:60%">
    <div id="graph_bound" style="height:400px; width:100%; position:relative; ">
      <div id="graph"></div>
      <div style="position:absolute; top:20px; right:30px;">

        <div class="btn-group">
          <button id="zoomin" class="btn" >+</button>
          <button id="zoomout" class="btn" >-</button>
          <button id="left" class="btn" ><</button>
          <button id="right" class="btn" >></button>
        </div>

      </div>

      <div id="stat" style="position:absolute; top:40px; right:30px;">
      </div>
    </div>
  </div>

  <div style="float:left; width:40%">
    <div id="pie" style="width:100%; height:400px;"></div>
  </div>

  <div style="clear:both;"></div>

</div>

   
<div id="ModalConfigure" class="modal hide keyboard" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
  <h3><?php echo _('Configure report'); ?></h3></div>
  <div id="widget_options_body" class="modal-body">

  <table>
  <tr>
    <td style="width:250px">
      <p><b><?php echo _('Select histogram feed:'); ?></b><br>
      <select id='modal-histogram-feed'></select></p>
    </td>
    <td>
      <p><b><?php echo _('Default period:'); ?></b><br>
      <select id='modal-default-period' >
        <option value="365" ><?php echo _('Year'); ?></option>
        <option value="30" ><?php echo _('Month'); ?></option>
        <option value="14" ><?php echo _('14 days'); ?></option>
        <option value="1" ><?php echo _('Week'); ?></option>
       </select>
    </td>
  </tr>

  <tr>
    <td>
      <p><b><?php echo _('Select first power range: 0W to '); ?></b><br>
      <input type='text' id='modal-thresholdA' style="width:200px"  /></p>
    </td><td>
      <p><b><?php echo _('Select end of mid power range:'); ?></b><br>
      <input type='text' id='modal-thresholdB' style="width:200px"  /></p>
    </td>
  </tr>

  <tr>
    <td>
      <p><b><?php echo _('Default mode:'); ?></b><br>
      <select id='modal-default-mode' >
        <option value="energy" ><?php echo _('Energy (kWh)'); ?></option>
        <option value="money" ><?php echo _('Money (GBP)'); ?></option>
       </select>
    </td>
    <td></td>
  </tr>

  <tr>
    <td>
      <p><b><?php echo _('Unit price:'); ?></b><br>
      <input type='text' id='modal-unitprice' style="width:200px" /></p>
    </td><td>
      <p><b><?php echo _('Fixed rate:'); ?></b><br>
      <input type='text' id='modal-fixedrate' style="width:200px" /></p>
    </td>
    <td></td>
  </tr>

  </table>

  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo _('Cancel'); ?></button>
    <button id="options-save" class="btn btn-primary"><?php echo _('Save changes'); ?></button>
  </div>  
  </div>  
</div>

<script>

  var path = "<?php echo $path; ?>";
  $('#graph').width($('#graph_bound').width());
  $('#graph').height($('#graph_bound').height());

  $(window).resize(function(){
    $('#graph').width($('#graph_bound').width());
    redraw();
  });

  // Load settings from database
  var settings = report.getsettings();

  // If the histogram feed is not specified then use defaults
  if (!settings) {
    settings = {
      histogramFeed: 0,
      thresholdA:1000,
      thresholdB:3000,
      defaultPeriod: 30,
      defaultMode: 'money',
      unitprice: 0.16,
      fixedrate: 0.0
    };
  }

  // Fill modal settings editor

  var feedlist = feed.list();
  var out = "<option value=0>Select histogram</option>";
  for (z in feedlist) {
    if (feedlist[z].datatype == 3) {
      var selected = ''; if (feedlist[z].id == settings.histogramFeed) selected = 'selected';
      out += "<option value="+feedlist[z].id+" "+selected+">"+feedlist[z].name+"</option>";
    }
  }
  $("#modal-histogram-feed").html(out);
  $("#modal-thresholdA").val(settings.thresholdA);
  $("#modal-thresholdB").val(settings.thresholdB);
  $("#modal-unitprice").val(settings.unitprice);
  $("#modal-fixedrate").val(settings.fixedrate);

  var period;
  var daysinview;
  var prependUnits, appendUnits, unitscale;
  var apikey='',timeWindow,start,end;

  var data = [];

  var numberOfSeries = 3;
  var labelcolor = [
    {label: "<?php echo _('Less than 1 kW'); ?>", color: "#4da74d"},  
    {label: "<?php echo _('1 kW to 3 kW'); ?>", color: "#edc240"}, 
    {label: "<?php echo _('More than 3 kW'); ?>", color: "#cb4b4b"}
  ];

  // Create histogram and piedata from labelcolor descriptor
  // use eval and JSON.stringify so that the resultant objects are not linked
  var series = eval(JSON.stringify(labelcolor));
  var piedata = eval(JSON.stringify(labelcolor));

  // Add bargraph series type to series object
  for (var s=0; s<numberOfSeries; s++) {
    series[s].stack = true;
    series[s].bars = { show: true,align: "center",barWidth: (3600*18*1000),fill: true };
  }

  init();
  redraw();

  function init()
  {
    // Money or energy
    if (settings.defaultMode == 'money') {
      prependUnits = "£"; 
      appendUnits = ""; 
      unitscale = settings.unitprice;
    } else {
      prependUnits = ""; 
      appendUnits = "kWh"; 
      unitscale = 1;
    }

    period = settings.defaultPeriod;
    timeWindow = (3600000*24.0*period);				//Initial time window
    start = ((new Date()).getTime())-timeWindow;		//Get start time
    end = (new Date()).getTime();				//Get end time

    // If histogram feed is 0 which indicates that settings have not been set
    // then create example dataset so that there is something to look at
    if (settings.histogramFeed == 0) {
      data = [];
      var exstart = (end/1000.0) - (3600*24.0*365*2);
      for (var i=0; i<(365*2); i++)
      {
        data[i] = [];
        data[i][0] = exstart + (i*24.0*3600);
        data[i][1] = 2.0+Math.random()*1;
        data[i][2] = 2-Math.sin(i*0.0172)*1*Math.random()*1;
        data[i][3] = 3-Math.sin(i*0.0172)*2;
      }
    } else {
      // If settings.histogramFeed is not zero then attempt to request data
      data = feed.get_kwhatpowers(settings.histogramFeed,[0,settings.thresholdA,settings.thresholdB,50000]);
    }
  }

  function redraw()
  {
    var sum = [];

    // Init and empty series object
    for (var s=0; s<numberOfSeries; s++) { 
      series[s].data = [];
      sum[s] = 0;
    }
 
    // Transfer data from single timestamp with multiple plots to seperate series each with their own timestamp.
    var i = 0;
    for (z in data) {
      var time = data[z][0]*1000;
      if (time>start && time<end) {
        for (var s=0; s<numberOfSeries; s++) {
          series[s].data[i] = [time,data[z][s+1]*unitscale];
          sum[s] += parseFloat(series[s].data[i][1]);
        }
        i++;
      }
    }

    daysinview = i;
    var mean = (sum[0] + sum[1] + sum[2]) / daysinview;
    series[3] = {color: "#999", data:[[start,mean],[end,mean]], lines: { fill: false }};

    // Draw stats block
    statsblock(daysinview);
   
    // Draw main graph
    $.plot($("#graph"), series, {
      grid: { show: true, hoverable: true, clickable: true },
      xaxis: { mode: "time", localTimezone: true, min: start, max: end, minTickSize: [1, "day"], tickLength: 1 },
      selection: { mode: "xy" },
      legend: { show: true, position: "nw"}
    });

    for (var s=0; s<numberOfSeries; s++) piedata[s].data = sum[s];

		$.plot("#pie", piedata, {
			series: {
				pie: { 
					show: true,
          radius: 2/3,
				  label: { formatter: labelFormatter },
          stroke: { width:3 }
				}
			},
      legend: { show: false },
      grid: {
          hoverable: true,
          clickable: true
      }
		});

  } // end of vis feed data

  //--------------------------------------------------------------------------------------
  // Stats block functions
  //--------------------------------------------------------------------------------------
  function statsblock(daysinview)   
  {
    var now = ((new Date()).getTime());
    var a = now-(3600000*24.0*period);
    var b = now;
    var last = useInRange(a, b);
    $("#last").html("<?php echo _('Last'); ?> "+period+" <?php echo _('days:'); ?> "+prependUnits+(last).toFixed(0)+appendUnits+" | "+prependUnits+(last/daysinview).toFixed(2)+appendUnits+"<?php echo _('/day'); ?>");
    b = a;
    var a = now-(3600000*24.0*period*2);
    var previous = useInRange(a, b);
    $("#previous").html("<?php echo _('Previous'); ?> "+period+" <?php echo _('days:'); ?> "+prependUnits+(previous).toFixed(0)+appendUnits+" | "+prependUnits+(previous/daysinview).toFixed(2)+appendUnits+"<?php echo _('/day'); ?>");

    var prc = ((last / previous)*100)-100;
    $("#prc-change").html(prc.toFixed(0)+"%");
    if (prc<0) $("#prc-change").css('color',"#4da74d"); else $("#prc-change").css('color',"#cb4b4b");
  }

  function useInRange(start, end)  
  {
    var sum = 0;
    for (z in data)
    {
      var time = data[z][0]*1000;
      if (time>start && time<end) {
        sum += parseFloat(data[z][1])*unitscale;
        sum += parseFloat(data[z][2])*unitscale;
        sum += parseFloat(data[z][3])*unitscale;
      }
    }
    return sum;
  }

  //--------------------------------------------------------------------------------------
  // Histogram daily graph functions
  //--------------------------------------------------------------------------------------

  // Graph zooming
  $("#graph").bind("plotselected", function (event, ranges) 
  {
     start = ranges.xaxis.from; end = ranges.xaxis.to;
     redraw();
  });

  // Operate buttons
  $("#zoomout").click(function () {inst_zoomout(); redraw();});
  $("#zoomin").click(function () {inst_zoomin(); redraw();});
  $('#right').click(function () {inst_panright(); redraw();});
  $('#left').click(function () {inst_panleft(); redraw();});
  $('.time').click(function () {period = $(this).attr("time"); inst_timewindow($(this).attr("time")); redraw();});

	$("#graph").bind("plothover", function (event, pos, item) {
	  if (item) {
			  $("#tooltip").remove();
        var val = 0;
        if (item.seriesIndex == 0) val = parseFloat(series[0].data[item.dataIndex][1]); 
        if (item.seriesIndex == 1) val = parseFloat(series[1].data[item.dataIndex][1]); 
        if (item.seriesIndex == 2) val = parseFloat(series[2].data[item.dataIndex][1]); 

			  showTooltip(item.pageX, item.pageY,item.series.label+": "+val.toFixed(1)+" kWh/d");
	  } else {
		  $("#tooltip").remove();
		  previousPoint = null;            
	  }
	});

  // Hover tooltip
	function showTooltip(x, y, contents) {
		$("<div id='tooltip'>" + contents + "</div>").css({
			position: "absolute",
			display: "none",
			top: y + 5,
			left: x + 5,
			border: "1px solid #fff",
			padding: "2px",
			"background-color": "#fefefe",
			opacity: 0.80
		}).appendTo("body").fadeIn(100);
	}

  //--------------------------------------------------------------------------------------
  // Pie chart functions
  //--------------------------------------------------------------------------------------

	function labelFormatter(label, thisseries) {
		return "<div style='font-size:11pt; text-align:center; padding:2px; color:#444; width:150px'>" + label+": <b>"+Math.round(thisseries.percent) + "%</b><br/ >" + prependUnits+thisseries.data[0][1].toFixed(0) +appendUnits+" <?php echo _('in'); ?> "+daysinview+" <?php echo _('days'); ?><br>"+prependUnits+(thisseries.data[0][1]/daysinview).toFixed(2) +appendUnits+"<?php echo _('/day'); ?></div>";
	}

  //--------------------------------------------------------------------------------------
  // Interface actions
  //--------------------------------------------------------------------------------------

  $("#money").click(function () {
    prependUnits = "£"; 
    appendUnits = ""; 
    unitscale = settings.unitprice;
    redraw();
  });

  $("#energy").click(function () {
    prependUnits = ""; 
    appendUnits = " kWh"; 
    unitscale = 1;
    redraw();
  });

  $("#options-save").click(function() {
    $('#ModalConfigure').modal('hide');

    settings = {
      histogramFeed: $("#modal-histogram-feed").val(),
      thresholdA: $("#modal-thresholdA").val(),
      thresholdB: $("#modal-thresholdB").val(),
      defaultPeriod: $("#modal-default-period").val(),
      defaultMode: $("#modal-default-mode").val(),
      unitprice: $("#modal-unitprice").val(),
      fixedrate: $("#modal-fixedrate").val()
    };

    report.setsettings(settings);

    init();
    redraw();
  });

  /*
  $("#graph").bind("plotclick", function (event, pos, item)
  {
    if (item!=null)
    {
      var start = 1*dataA[item.dataIndex][0];
      var end = start + (3600000*24.0);
      var power_data = feed.get_data(36,start,end,500);

      $.plot($("#graph"), [{data: power_data, lines: { show: true, fill: true }}], {
        grid: { show: true, hoverable: true, clickable: true },
        xaxis: { mode: "time", localTimezone: true, min: start, max: end },
        selection: { mode: "xy" }
      });
    }
  });*/

</script>

<?php require "applist.php"; ?>
