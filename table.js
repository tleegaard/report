/*

  table.js is released under the GNU Affero General Public License.
  See COPYRIGHT.txt and LICENSE.txt.

  Part of the OpenEnergyMonitor project:
  http://openenergymonitor.org
 
*/

var table = {

    'data':0,
    'groupshow':{},

    'eventsadded':false,
    'deletedata':true,

    'sortfield':null,

    'total_watthours':0,
    'energyrange': [],
     
    'draw':function()
    {
        // Hack to display watt-hours
        table.total_watthours = 0;
        var group_watthours = {};

        var sum = [0,0,0];
        var thresholdA = 1000;
        var thresholdB = 3000;

        for (row in table.data)
        {
          // Main calculation
          if (!table.data[row]['watthours']) table.data[row]['watthours'] = 0;
          if (table.data[row]['power'] && table.data[row]['hours']) {
             table.data[row]['watthours'] = parseInt(table.data[row]['power'] * table.data[row]['hours']);

             // Calculate energy used at power ranges
             if (table.data[row]['power']>0 && table.data[row]['power']<thresholdA) sum[0] += table.data[row]['watthours'];
             if (table.data[row]['power']>thresholdA && table.data[row]['power']<thresholdB) sum[1] += table.data[row]['watthours'];
             if (table.data[row]['power']>thresholdB && table.data[row]['power']<30000) sum[2] += table.data[row]['watthours'];
          }
          if (!group_watthours[table.data[row]['category']]) group_watthours[table.data[row]['category']] = 0;
          group_watthours[table.data[row]['category']] += parseInt(table.data[row]['watthours']);
          table.total_watthours += parseInt(table.data[row]['watthours']);
        }

        table.energyrange[0] = sum[0];
        table.energyrange[1] = sum[1];
        table.energyrange[2] = sum[2];

        var group_num = 0;
        var groups = {};
        for (row in table.data)
        {
            var group = table.data[row][table.groupby];
            if (!group) group = 'NoGroup';
            if (!groups[group]) {groups[group] = ""; group_num++;}
            groups[group] += table.draw_row(row);
        }

        var html = "";

        for (field in table.fields)
        {
          var title = field; if (table.fields[field].title!=undefined) title = table.fields[field].title;
          //html += "<th><a type='sort' field='"+field+"'>"+title+"</a></th>";
        }

        for (group in groups) 
        {
            // Minimized group persistance, see lines: 4,92,93
            var visible = '', symbol ='-'; 
            if (table.groupshow[group]==false) {symbol = '+'; visible = "display:none";}

            html += "<tr><th>"+group+"</th><th></th><th></th><th>"+group_watthours[group]+" Wh</th><th></th><th></th></tr>";

            html += "<tbody id='"+group+"' style='"+visible+"'><tr>";

            html += "</tr>";
            html += groups[group];
            html += "</tbody>";
        }

        // Hack to display total
        html += "<tr class='trtotal'><td></td><td></td><td>Total:</td><td>"+table.total_watthours+" Wh</td><td></td></tr>";

        $(table.element).html("<table class='table'>"+html+"</table>");

        if (table.eventsadded==false) {table.add_events(); table.eventsadded = true}
    },

    'draw_row': function(row)
    {
        var html = "<tr uid='"+row+"' >";
        for (field in table.fields) html += "<td row='"+row+"' field='"+field+"' >"+table.fieldtypes[table.fields[field].type].draw(row,field)+"</td>";
        html += "</tr>";
        return html;
    },
        
    'update':function(row,field,value)
    {
        table.data[row][field] = value;
        var type = table.fields[field].type;
        if(typeof table.fieldtypes[type].draw === 'function') {
          $("[row="+row+"][field="+field+"]").html(table.fieldtypes[type].draw(row,field));
        }
    },
  
    'remove':function(row)
    {
        table.data.splice(row,1);
        table.draw();
    },

    'sort':function(field,dir)
    {
        table.sortfield = field; 
        table.data.sort(function(a,b) {
          if(a[field]<b[field]) return -1*dir;
          if(a[field]>b[field]) return 1*dir;
          return 0;
        });
        table.draw();
    },

   'add_events':function()
    {

        // Event: minimise or maximise group
        $(table.element).on('click', '.MINMAX', function() {
            var group = $(this).attr('group');
            var state = $(this).html();
            if (state == '-') { $("#"+group).hide(); $(this).html('+'); table.groupshow[group] = false; }
            if (state == '+') { $("#"+group).show(); $(this).html('-'); table.groupshow[group] = true; }
        });

        // Event: sort by field
        $(table.element).on('click', 'a[type=sort]', function() {
            var field = $(this).attr('field');
            table.sort(field,1);
            console.log(field);
        });

        // Event: delete row
        $(table.element).on('click', 'a[type=delete]', function() {
            if (table.deletedata) table.remove( $(this).attr('row') );
            $(table.element).trigger("onDelete",[$(this).attr('uid'),$(this).attr('row')]);
        });

        // Event: inline edit
        $(table.element).on('click', 'a[type=edit]', function() {
            var mode = $(this).attr('mode');
            var row = $(this).attr('row');
            var uid = $(this).attr('uid');

            // Trigger events
            if (mode=='edit') $(table.element).trigger("onEdit");

            var fields_to_update = {};

            for (field in table.fields) 
            {
                var type = table.fields[field].type;

                if (mode == 'edit' && typeof table.fieldtypes[type].edit === 'function') {
                    $("[row="+row+"][field="+field+"]").html(table.fieldtypes[type].edit(row,field));
                }

                if (mode == 'save' && typeof table.fieldtypes[type].save === 'function') {
                  var value = table.fieldtypes[type].save(row,field);
                  if (table.data[row][field] != value) fields_to_update[field] = value;	// only update db if value has changed
                  table.update(row,field,value); 	// but update html table because this reverts back from <input>		
                }
            }

            // Hack to run watt hour calculations on update
            if (mode == 'save') table.draw();

            // Call onSave event only if there are fields to be saved
            if (mode == 'save' && !$.isEmptyObject(fields_to_update))
            {
              $(table.element).trigger("onSave",[uid,fields_to_update]);
              if (fields_to_update[table.groupby]!=undefined) table.draw();
            }

            if (mode == 'edit') {$(this).attr('mode','save'); $(this).html("<i class='icon-ok' ></i>");}
            if (mode == 'save') {$(this).attr('mode','edit'); $(this).html("<i class='icon-pencil' ></i>");}


        });

        // Check if events have been defined for field types.
        for (i in table.fieldtypes)
        {
            if (typeof table.fieldtypes[i].event === 'function') table.fieldtypes[i].event();
        }
    },

    /*

    Field type space
 
    */
  
    'fieldtypes':
    {
        'fixed':
        {
            'draw': function (row,field) { return table.data[row][field] }
        },

        'text':
        {
            'draw': function (row,field) { return table.data[row][field] },
            'edit': function (row,field) { return "<input type='text' value='"+table.data[row][field]+"' / >" },
            'save': function (row,field) { return $("[row="+row+"][field="+field+"] input").val() },
        },

        'textlink':
        {
            'draw': function (row,field) { return "<a href='"+table.fields[field].link+table.data[row]['id']+"' >"+table.data[row][field]+"</a>" },
            'edit': function (row,field) { return "<input type='text' style='width:120px' value='"+table.data[row][field]+"' / >" },
            'save': function (row,field) { return $("[row="+row+"][field="+field+"] input").val() },
        },

        'select':
        {
            'draw': function (row,field) { return table.fields[field].options[table.data[row][field]] },
            'edit': function (row,field) { 
                var options = "";
                for (option in table.fields[field].options) 
                {
                  var selected = ''; if (option==table.data[row][field]) selected = 'selected';
                  options += "<option value='"+option+"' "+selected+" >"+table.fields[field].options[option]+"</option>";
                }
                return "<select style='width:120px'>"+options+"</select>";
            },
            'save': function (row,field) { return $("[row="+row+"][field="+field+"] select").val() },
        },

        'checkbox':
        {
            'draw': function (row,field) { return table.data[row][field] },
            'edit': function (row,field) { return "<input type='checkbox'>" },
            'save': function (row,field) { return $("[row="+row+"][field="+field+"] input").prop('checked') },
        },

        'delete':
        {
            'draw': function (row,field) { return "<a type='delete' row='"+row+"' uid='"+table.data[row]['id']+"' ><i class='icon-trash' ></i></a>"; }
        },

        'edit':
        {
            'draw': function (row,field) { return "<a type='edit' row='"+row+"' uid='"+table.data[row]['id']+"' mode='edit'><i class='icon-pencil' ></i></a>"; }
        },
    }
}


