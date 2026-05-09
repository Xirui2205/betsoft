<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body>

       <!-- Start of Group-Office Login -->
             <form method="post" action="javascript:open_new_window()" name="login">
             <input type="hidden" name="task" value="login">
             <input type="hidden" name="auth_source_key" value="0">
             <input type="hidden" name="SET_LANGUAGE" class="textbox" value="en-us">
             <input id="11086669304214ea3284d1e" type="hidden" name="remind" value="true">
             <input type="text" class="textbox" name="username" value="" size="10">
             <input class="textbox" type="password" name="password" value="" size="10">
             <input type="submit" class="button" value="Login" style="width: 50px;" onclick="win=window.open('','myWin','width=640,height=500,left=0,top=0,scrollbars=auto,resizable=yes'); this.form.target='myWin';this.form.action='https://webmail.compbet.com/index.php'">
             </form>
       <!-- End of Group-Office Login -->4

    <form method="post" action="https://webmail.compbet.com/index.php" name="login" target="_parent">
    <input type="hidden" name="task" value="login" />
    <table cellspacing="2" border="0" cellpadding="1" valign="middle" align="center" style="margin: 20px;">
    <tr>
    <td colspan="2" align="center"><h1>Group-Office</h1></td>
    </tr>
    <tr>
    <td colspan="2" align="center">
    <h3>Enter your username and password to login</h3>

    <br />
    </td>
    </tr>
    <tr>
    <td align="right" nowrap>
    Username:&nbsp;
    </td>
    <td>
      <input type="hidden" name="auth_source_key" value="0" /><input type="text" class="textbox" name="username" value="" size="30" /></td>

    </tr>
    <tr>
    <td align="right" nowrap>
    Password:&nbsp;
    </td>

    <td>
    <input class="textbox" type="password" name="password" value="" size="30" />
    </td>
    </tr>
    <tr>
    <td align="right" nowrap>
    Language:&nbsp;
    </td>
    <td>
    <select name="SET_LANGUAGE" class="textbox" onchange="javascript:set_language(this)"><option value="bg">Bulgaria</option><option value="zh_TW_big5">Chinese</option><option value="da">Dansk</option><option value="da-dk">Dansk/Danmark</option><option value="de">Deutsch</option><option value="de-at">Deutsch/Austria</option><option value="de-ch">Deutsch/Switzerland</option><option value="en" selected>English</option><option value="en-au">English/Australia</option><option value="en-gb">English/Great Britain</option><option value="en-nz">English/New Zealand</option><option value="en-us">English/United States</option><option value="fr">Francais</option><option value="he">Hebrew</option><option value="it">Italiano</option><option value="nl">Nederlands</option><option value="no">Norsk</option><option value="pt">Portugues</option><option value="pt-br">Portugues/Brazil</option><option value="sr">Serbian</option><option value="sl">Slovenski</option><option value="es">Spanish</option><option value="sv">Svenska</option></select></td>

    </tr>
    <tr>
    <td colspan="2">
    <br />
    <input id="110627246241f060cee565e" type="checkbox" name="remind" value="true"  /><a href="javascript:check_checkbox('110627246241f060cee565e')">Keep me logged in till I press the logout button.</a></td>
    </tr>

    <tr>
    <td colspan="2" align="center">
    <br />
    <input type="button" class="button" style="width: 100px;" value="Login" onclick="javascript:document.forms[0].submit();" onmouseover="javascript:this.className='button_mo';" onmouseout="javascript:this.className='button';" /></td>
    </tr>
    </table>

    <script type="text/javascript" language="javascript">
    var nav4 = window.Event ? true : false;
    function processkeypress(e)
    {
      if(nav4)
      {
        var whichCode = e.which;
      }else
      {
        var whichCode = event.keyCode;
      }

      if (whichCode == 13)
      {
        window.document.forms[0].submit();
        return true;
      }
    }
    if (window.Event) //if Navigator 4.X
    {
      document.captureEvents(Event.KEYPRESS)
    }
    document.onkeypress = processkeypress;
    document.forms[0].username.focus();

    function set_language(dropbox)
    {
      document.location='/group-office/index.php?SET_LANGUAGE='+dropbox.value;
    }
    </script>
    </form>


</body>
</html>