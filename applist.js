
var applist = {

  'getlists':function()
  {
    var result = {};
    $.ajax({ url: path+"report/getlists.json", async: false, dataType: 'json', success: function(data){result = data;} });
    return result;
  },

  'get':function(id)
  {
    var result = {};
    $.ajax({ url: path+"report/getapplist.json", data: "id="+id, async: false, dataType: 'json', success: function(data){result = data;} });
    return result;
  },

  'set':function(id,listname,list)
  {
    var result = {};
    $.ajax({ url: path+"report/setapplist.json", data: "id="+id+"&name="+listname+"&list="+JSON.stringify(list), async: false, dataType: 'json', success: function(data){result = data;} });
    return result;
  }
,

  'remove':function(id)
  {
    var result = {};
    $.ajax({ url: path+"report/deleteapplist.json", data: "id="+id, async: false, dataType: 'json', success: function(data){result = data;} });
    return result;
  }

}

