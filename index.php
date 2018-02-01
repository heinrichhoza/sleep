<?php
// main page
// goal: several ppl browse to this page (exactly once each) and take turns clicking the button, which is disabled for anyone whose turn it isn't.

include("sess_id.php");
session_start();

if (!isset($_SESSION['max_id']))
  $_SESSION['max_id'] = 0;

$id = $_SESSION['max_id'];
$_SESSION['max_id']++;
?>
<div id="debug" style="float: right">
  debug data:
</div>
<button id="button" type="button" onclick="tick();"></button><br />
<br />
<a href="../index.php">home</a>

<script type="text/javascript">
  var first = true;
  
  function get_value(_current, _default)
  {
    if (_current == undefined || _current == null)
      return _default;
    
    return _current;
  }
  
  function _request_obj (rel_path, method, params, async, on_load_function)
  {
    this.on_load_function = on_load_function;
    this._xml_request     = new XMLHttpRequest();
    this.index            = _requests.length;
    var self = this;
    var base_url = "http://heinrich.hoza.us/sleep/";
    
    method = get_value(method, "GET").toUpperCase();
    async  = get_value(async,  false);
    
    this._xml_request.open(method, base_url + rel_path, async);
    this._xml_request.setRequestHeader("User-Agent",navigator.userAgent);
    
    if (method == "POST")
    {
      this._xml_request.setRequestHeader("Content-type",   "application/x-www-form-urlencoded");
      this._xml_request.setRequestHeader("Content-length", params.length);
    }
    
    if (async == true && typeof(this.on_load_function) == "function")
    {
      this._xml_request.onreadystatechange = function()
      {
        _requests[self.index] = null;
        if (self._xml_request.readyState == 4 && self._xml_request.status == 200)
          self.on_load_function(self._xml_request.responseText);
      }
    }
  
    this._xml_request.send(params);
  }
  
  var _requests = [];
  
  function send_request(rel_path, method, params, async, on_load_fn)
  {
    var _request = new _request_obj(rel_path, method, params, async, on_load_fn);
    _requests[_requests.length] = _request;
    
    
    if (async == undefined || async == false)
      return _request._xml_request.responseText;
  }
  
  function is_numeric(n) // named the same as the php function for some sort of consistency
  {
    return !isNaN(parseFloat(n)) && isFinite(n);
  }
  
  function tick()
  {
    var button       = document.getElementById("button");
    button.enabled   = false;
    button.innerHTML = "please wait.";
    
    send_request
    (
      "tick.php",
      "POST",
      "tick=<?=$id?>",
      true,
      function(response)
      {
        if (response == "<?=$id?>")
        {
          button.enabled = true;
          button.innerHTML = "your turn.  click me.";
        }
        else
        {
          alert("?\n" + response);
        }
      }
    );
  }
  
  function tock()
  {
    send_request
    (
      "tock.php",
      "POST",
      "tock=<?=$id?>",
      true,
      function(response)
      {
        if (first != undefined && first == true) // declared at the beginning of this <script> tag
        {
          first = false;
          tick();
        }
        
        document.getElementById("debug").innerHTML = "debug data:<br />\n<pre />\n" + response + "</pre>";
        setTimeout("tock();", 500);
      }
    );
    
  }
  
  tock();
</script>