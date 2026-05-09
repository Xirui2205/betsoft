<?

class MobilemAPI
{
  function MobilemAPI($user,$passwd)
  {
    $this->user=$user;
    $this->passwd=md5($passwd);
  }
  
  function fetchXML($data,$default=Array())
  { 
    if (is_local())
    {   
      $body=$default;
    } else
    {
      
      $post=Array();
      foreach($data as $k => $v)
      {
        $post[count($post)]=$k."=".urlencode($v);
      }
      
//printr($post);
      $ch = curl_init(XMLAPI_URL);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, implode("&",$post));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $body=curl_exec($ch);
//printr($body);
    
      $err=curl_errno($ch);
      if ($err==0)
      {
        $err=curl_getinfo($ch,CURLINFO_HTTP_CODE);    
      }
      if (!$err) $err=0;
      
      curl_close ($ch);
          
    }
    
    if ($body)
    {
      $xml=new XMLParser('iso-8859-1',$body);
      return $xml->result;
    } else
    return Array();
  }
  
  function AuthData($msg,$action)
  {
    $data=Array();
    $data['action']=$action;
    $data['msg']=$msg;    
    $data['login']=$this->user;    
    $data['auth']=md5($this->passwd.$this->user.$action.substr($msg,0,31));
    return $data;
  }
  
  function Info()
  {
    $data=$this->AuthData(gen_code(35),"info");
    $res=$this->fetchXML($data,
     "<?xml version=\"1.0\" encoding=\"windows-1250\" ?>\n".
     "<mobilem_api status=\"ok\">".
     "<sid>0VT67QserCm9Rt1g8YT</sid> ".
     "<login>trosa</login> ".
     "<name>Tomáš</name> ".
     "<surname>Rosa</surname> ".
     "<email>info@compbet.com</email> ".
     "<credit>271.81</credit> ".
     "<lastincome>2004-05-04 01:02:37</lastincome> ".
     "<auth_phone>0</auth_phone> ".
     "<auth_email>1</auth_email> ".
     "<account_number>100000044</account_number> ".
     "<bank_number>1111/2400</bank_number> ".
     "<nick>trosa</nick> ".
     "<nick_active>yes</nick_active> ".
     "</mobilem_api>");
     
    //return $res['mobilem_api'];
  }
  
  function Send($msg, $numbers, $recack=0, $recackaddr='', $delay='', $waitfordelivery=1, $nosave=0, $split='concat')
  {
    if (is_array($numbers)) $numbers=implode(",",$numbers);
    $data=$this->AuthData($msg,"send");
    $data['msisdn']=$numbers;
    $data['recack']=$recack;
    $data['recackaddr']=$recackaddr;
    $data['delay']=$delay;
    $data['waitfordelivery']=$waitfordelivery;
    $data['nosave']=$nosave;
    $data['split']=$split;
    
    if (!$numbers)
    {
      $def="<?xml version=\"1.0\" encoding=\"windows-1250\" ?>\n".
        "<mobilem_api status=\"error\">".
        "<error>".
        "<code>906</code> ".
        "<message>Telefonní èíslo pøíjemce není platné</message> ".
        "</error>".
        "</mobilem_api>";
    } else
    {
      $def="<?xml version=\"1.0\" encoding=\"windows-1250\" ?>\n".
        "<mobilem_api status=\"ok\">".
        "<sid>Dgi1B0bDOtJufGxtfs2</sid> ".
        "<smsid>27006</smsid> ".
        "<price>0</price> ".
        "<credit>271.81</credit> ".
        "<parts>1</parts> ".
        "<recackaddr /> ".
        "<gwref>-1</gwref>".
        "</mobilem_api>";
    }
    
    $res=$this->fetchXML($data,$def);
     
    return $res['mobilem_api'];
  }
  
  
}


?>