<body style="font-family: 'Source Sans Pro', sans-serif;width:100% !important; margin:0 auto;">
<!--<body style="font-family: 'Source Sans Pro', sans-serif;width:910px; margin:0 auto; background:url(assets/pdf_templates/logo/sertifikatas_v7.svg)">-->
<!--<body style="font-family: 'Source Sans Pro', sans-serif;width:910px; margin:0 auto; background:url('data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='10'><linearGradient id='gradient'><stop offset='10%' stop-color='%23F00'/><stop offset='90%' stop-color='%23fcc'/> </linearGradient><rect fill='url(%23gradient)' x='0' y='0' width='100%' height='100%'/></svg>');">
<body style="font-family: 'Source Sans Pro', sans-serif;width:910px; margin:0 auto; ">-->


<div style="width:100%; ">  
  <div style="width:48%;float:left;">&nbsp; </div>
  <div style="width:48%;float:left;text-align:right;">
    <h2>&nbsp;</h2>
  </div>
</div>
<div style="width:100%;float:left;">
  <div style=" width:100%; margin:0 auto; height:244px">
    <div style="width: 48%;float:left;border-radius:10px;margin-right:10%;"> 	    
	<span> <h3 style="font-size:21px; color:#58595b; text-transform:uppercase; font-weight:light; margin-top:20px;">Animal Data</h3> </span>	
	{ANIMAL_DATA}	
	</div>
    <div style="width:48%;float:right;border-radius:10px;"> 	   
	<span>           <h3 style="font-size:21px; color:#58595b; text-transform:uppercase; font-weight:light;margin-top:20px; padding-left:22px">Sample Data</h3>        </span>	 
	
	{SAMPLE_DATA}    </div>
  </div>
</div>
<div style="width:100%;clear:both; height:207px">
   <span><h3 style="font-size:21px; text-transform:uppercase; font-weight:light;margin-top:0px;">DNA PROFILE </h3>    </span>      
      <div style=" width:100%; margin:0px auto 0; ">    
    {DNA_PROFILE}   
      </div>
</div>
<div style="clear:both;width:100%;float:left; ">
 <span >
    <h3 style="font-size:21px;  text-transform:uppercase; font-weight:light;margin-top:0; margin-bottom:15px">DNA MARKERS </h3>
    </span>
  <div style=" width:100%; margin:0 auto"> 
    <div style="clear:both;width:100%;float:left;min-height:267px;">
      <div style="padding:10px;float:left;"> {DNA_MARKERS_TABLE_DATA} </div>
    </div>
  </div>
</div>
<div style="width:100%;clear:both; height:180px">
  <div style=" width:100%; margin:0 auto"> <span>
    <h3 style="font-size:21px; color:#fff; text-transform:uppercase; font-weight:light;margin-top:40px; margin-bottom:0;">CERTIFIED BY </h3>
    </span>
    <div style="width:510px;float:left;border-radius:10px;padding:2px;">
      <div style="float:left;width:48%;">
	       <div style="float:left;width:100%;">&nbsp;</div>
		   <div style="float:left;width:100%;padding-top:43px; ">dr.van haeringen laboratorium b.v <br> a VHL Genetics Company</div>
	  </div>
      <div style="float:left;width:52%;">
        <div style="width:100%; padding-top:27px; ">
        <div style="height:65px">&nbsp;</div>
          <div style="width:45%;float:left;width:30%;font-weight:bold; font-size:13px;color:#58595b;line-height:light"></div>
          <div style="width:55%;float:right;font-weight:bold; font-size:11px;color:#58595b;line-height:1.3; margin-top:8px">Certificate Nr: {CERTIFIED_NO}
            <div style="width:100%;float:right;margin-top:3px">Date of Issue: {DATE_OF_ISSUE} </div></div>
        </div>
      </div>
    </div>
    <div style="width:18%;float:right;height:auto;">{QR_CODE}</div>
   </div>
</div>

</body>

