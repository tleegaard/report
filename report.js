
var report = {

  'setsettings':function(settings)
  {
    var result = {};
    $.ajax({ url: path+"report/setsettings.json", data: "settings="+JSON.stringify(settings), async: false, dataType: 'json', success: function(data){result = data;} });
    return result;
  },

  'getsettings':function()
  {
    var result = {};
    $.ajax({ url: path+"report/getsettings.json", async: false, dataType: 'json', success: function(data){result = data;} });
    return result;
  }

}

