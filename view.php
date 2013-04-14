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
    <button class="btn time" time="365" >Year</button>
    <button class="btn time" time="30" ">Month</button>
    <button class="btn time" time="14" ">2 Weeks</button>
    <button class="btn time" time="7" ">Week</button>
  </div>

  <div class="btn-group" style="float:right; margin-right:10px;">
    <button id="money" class="btn">£</button>
    <button id="energy" class="btn">kWh</button>
  </div>

  <h1>My Electric</h1>
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
      <p><b>Select histogram feed:</b><br>
      <select id='modal-histogram-feed'></select></p>
    </td>
    <td>
      <p><b>Default period:</b><br>
      <select id='modal-default-period' >
        <option value="365" >Year</option>
        <option value="30" >Month</option>
        <option value="14" >14 days</option>
        <option value="1" >Week</option>
       </select>
    </td>
  </tr>

  <tr>
    <td>
      <p><b>Select first power range: 0W to </b><br>
      <input type='text' id='modal-thresholdA' style="width:200px"  /></p>
    </td><td>
      <p><b>Select end of mid power range:</b><br>
      <input type='text' id='modal-thresholdB' style="width:200px"  /></p>
    </td>
  </tr>

  <tr>
    <td>
      <p><b>Default mode:</b><br>
      <select id='modal-default-mode' >
        <option value="energy" >Energy (kWh)</option>
        <option value="money" >Money (£)</option>
       </select>
    </td>
    <td></td>
  </tr>

  <tr>
    <td>
      <p><b>Unit price:</b><br>
      <input type='text' id='modal-unitprice' style="width:200px" /></p>
    </td><td>
      <p><b>Fixed rate:</b><br>
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
  var dataA = [];
  var dataB = [];
  var dataC = [];
  //var dataD = [];


  init();
  vis_feed_data();

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

  $(window).resize(function(){
    $('#graph').width($('#graph_bound').width());
    //if (embed) $('#graph').height($(window).height());
    vis_feed_data();
  });

  function vis_feed_data()
  {

    dataA = [];
    dataB = [];
    dataC = [];
    //dataD = [];

    var sum = 0.0; 
    var sumA = 0, sumB = 0, sumC = 0, sumD = 0;
    var i = 0;

    for (z in data)
    {
      var time = data[z][0]*1000;
      if (time>start && time<end)
      {
      dataA[z] = [];
      dataB[z] = [];
      dataC[z] = [];
      //dataD[z] = [];

      dataA[z][0] = time;
      dataA[z][1] = data[z][1]*unitscale;
      sum += parseFloat(dataA[z][1]);
      sumA += parseFloat(dataA[z][1]);

      dataB[z][0] = time;
      dataB[z][1] = data[z][2]*unitscale;
      sum += parseFloat(dataB[z][1]);
      sumB += parseFloat(dataB[z][1]);

      dataC[z][0] = time;
      dataC[z][1] = data[z][3]*unitscale;
      sum += parseFloat(dataC[z][1]);
      sumC += parseFloat(dataC[z][1]);

      //dataD[z][0] = time;
      //dataD[z][1] = data[z][4];
      //sum += parseFloat(dataD[z][1]);
      //sumD += parseFloat(dataD[z][1]);
      i++;
      }
    }

    var mean = sum / i;

    daysinview = i;

    var a = ((new Date()).getTime())-(3600000*24.0*period);
    var b = (new Date()).getTime();
    var last = useInRange(a, b);
    $("#last").html("Last "+period+" days: "+prependUnits+(last).toFixed(0)+appendUnits+" | "+prependUnits+(last/i).toFixed(2)+appendUnits+"/day");
    b = a;
    var a = ((new Date()).getTime())-(3600000*24.0*period*2);
    var previous = useInRange(a, b);
    $("#previous").html("Previous "+period+" days: "+prependUnits+(previous).toFixed(0)+appendUnits+" | "+prependUnits+(previous/i).toFixed(2)+appendUnits+"/day");

    var prc = ((last / previous)*100)-100;
    $("#prc-change").html(prc.toFixed(0)+"%");
    if (prc<0) $("#prc-change").css('color',"#4da74d"); else $("#prc-change").css('color',"#cb4b4b");

    var meanline = [[start,mean],[end,mean]];

    $.plot($("#graph"), [
      {label: "Less than 1 kW", color: "#4da74d", data:dataA ,stack: true, bars: { show: true,align: "center",barWidth: (3600*18*1000),fill: true }},  
      {label: "1 kW to 3 kW", color: "#edc240", data:dataB ,stack: true, bars: { show: true,align: "center",barWidth: (3600*18*1000),fill: true }}, 
      {label: "More than 3 kW", color: "#cb4b4b", data:dataC ,stack: true, bars: { show: true,align: "center",barWidth: (3600*18*1000),fill: true }}, 
      //{color: "", data:dataD ,stack: true, bars: { show: true,align: "center",barWidth: (3600*18*1000),fill: true }}, 
      {color: "#999", data:meanline, lines: { fill: false }}
    ], 
    {
      grid: { show: true, hoverable: true, clickable: true },
      xaxis: { mode: "time", localTimezone: true, min: start, max: end, minTickSize: [1, "day"], tickLength: 1 },
      selection: { mode: "xy" },
      legend: { show: true, position: "nw"}
    });

	  var piedata = [];

		piedata[0] = {
			label: "Less than 1 kW",
			data: sumA,
      color: "#4da74d"
		}

		piedata[1] = {
			label: "1 kW to 3 kW",
			data: sumB,
      color: "#edc240"
		}

		piedata[2] = {
			label: "More than 3 kW",
			data: sumC,
      color: "#cb4b4b"
		}

		//piedata[3] = {
		//	label: "More than 3 kW",
		//	data: sumD,
    //  color: "#cb4b4b"
		//}


		$.plot("#pie", piedata, {
			series: {
				pie: { 
					show: true,
          radius: 2/3,
				  label: {
            formatter: labelFormatter,
            //background: { 
            //    opacity: 0.5,
            ///    color: '#000'
            //}
          },
          stroke: {
              width:3,
          }
				}
			},
      legend: {
          show: false
      },

      grid: {
          hoverable: true,
          clickable: true
      }
		});
  }

  //--------------------------------------------------------------------------------------
  // Graph zooming
  //--------------------------------------------------------------------------------------
  $("#graph").bind("plotselected", function (event, ranges) 
  {
     start = ranges.xaxis.from; end = ranges.xaxis.to;
     vis_feed_data();
  });

  //----------------------------------------------------------------------------------------------
  // Operate buttons
  //----------------------------------------------------------------------------------------------
  $("#zoomout").click(function () {inst_zoomout(); vis_feed_data();});
  $("#zoomin").click(function () {inst_zoomin(); vis_feed_data();});
  $('#right').click(function () {inst_panright(); vis_feed_data();});
  $('#left').click(function () {inst_panleft(); vis_feed_data();});
  $('.time').click(function () {period = $(this).attr("time"); inst_timewindow($(this).attr("time")); vis_feed_data();});
  //-----------------------------------------------------------------------------------------------

	function labelFormatter(label, series) {
		return "<div style='font-size:11pt; text-align:center; padding:2px; color:#444; width:150px'>" + label+": <b>"+Math.round(series.percent) + "%</b><br/ >" + prependUnits+series.data[0][1].toFixed(0) +appendUnits+" in "+daysinview+" days<br>"+prependUnits+(series.data[0][1]/daysinview).toFixed(2) +appendUnits+"/day</div>";
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

  $("#money").click(function () {
    prependUnits = "£"; 
    appendUnits = ""; 
    unitscale = settings.unitprice;
    vis_feed_data();
  });

  $("#energy").click(function () {
    prependUnits = ""; 
    appendUnits = " kWh"; 
    unitscale = 1;
    vis_feed_data();
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
    vis_feed_data();
  });

</script>
