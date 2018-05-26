<body style="font-family: 'Source Sans Pro', sans-serif; background:url(assets/pdf_templates/logo/sertifikatas.svg) no-repeat; position: absolute;top: 0;left: 0;width: 100%;height: 100%;background-image-resize: 5;background-position: top center;">
<!--<body style="font-family: 'Source Sans Pro', sans-serif;width:910px; margin:0 auto; background:url(assets/pdf_templates/logo/sertifikatas_v7.svg)">-->
<!--<body style="font-family: 'Source Sans Pro', sans-serif;width:910px; margin:0 auto; background:url('data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='10'><linearGradient id='gradient'><stop offset='10%' stop-color='%23F00'/><stop offset='90%' stop-color='%23fcc'/> </linearGradient><rect fill='url(%23gradient)' x='0' y='0' width='100%' height='100%'/></svg>');">
<body style="font-family: 'Source Sans Pro', sans-serif;width:910px; margin:0 auto; ">-->


<div style="width:100%;">  
  <div style="width:48%;float:left;">&nbsp; </div>
  <div style="width:48%;float:left;text-align:right;">
    <h2>&nbsp;</h2>
  </div>
</div>
<div style="width:100%;float:left; margin-top:65px">
  <div style=" width:100%; margin:0 auto; height:225px">
    <div style="width: 48%;float:left;border-radius:10px;margin-right:10%;"> 	    
	<!--<span> <h3 style="font-size:21px; color:#58595b; text-transform:uppercase; font-weight:light; margin-top:20px;">Animal Data</h3> </span>	   -->
	{ANIMAL_DATA}	
	</div>
    <div style="width:48%;float:right;border-radius:10px;"> 	   
	<!--<span>           <h3 style="font-size:21px; color:#58595b; text-transform:uppercase; font-weight:light;margin-top:20px; padding-left:22px">Sample Data</h3>        </span>	    -->
	
	{SAMPLE_DATA}    </div>
  </div>
</div>
<div style="width:100%;clear:both; height:205px">
  <div style=" width:100%; margin:0px auto 0; ">    
<!--  <span>      <h3 style="font-size:21px; color:#fff; text-transform:uppercase; font-weight:light;margin-top:0px;">DNA PROFILE </h3>    </span>       -->
  
  {DNA_PROFILE}
    </div></div>
<div style="clear:both;width:100%;float:left; ">
  <div style=" width:100%; margin:0 auto"> 
  <!--<span style="">
    <h3 style="font-size:21px; color:#fff; text-transform:uppercase; font-weight:light;margin-top:0; margin-bottom:15px">DNA MARKERS </h3>
    </span>-->
    <div style="clear:both;width:100%;float:left;min-height:245px;">
      <div style="padding:10px;float:left;"> {DNA_MARKERS_TABLE_DATA} </div>
    </div>
  </div>
</div>
<div style="width:100%;clear:both; height:180px">
  <div style=" width:100%; margin:0 auto"> <span>
    <h3 style="font-size:21px; color:#fff; text-transform:uppercase; font-weight:light;margin-top:40px; margin-bottom:0;"> </h3>
    </span>
    <div style="width:510px;float:left;border-radius:10px;padding:2px;">
      <div style="float:left;width:48%;">&nbsp;</div>
      <div style="float:left;width:52%;">
        <div style="width:100%; padding-top:27px; ">
        <div style="height:65px">&nbsp;</div>
          <div style="width:50%;float:left;width:30%;font-weight:300; font-size:13px;color:#58595b;line-height:light"> &nbsp; </div>
          <div style="width:100%;float:right;font-weight:300; font-size:13px;color:#58595b;line-height:1.3; margin-top:8px; text-indent:20px">Certificate Nr : <span style="font-weight:bold">{CERTIFIED_NO}</span>
            <div style="width:100%;float:right;margin-top:3px; text-align:left;font-weight:300; margin-left:50px ">Date of Issue : <span style="font-weight:bold">{DATE_OF_ISSUE}</span> </div></div>
        </div>
      </div>
    </div>
    <div style="width:17%;float:right;height:auto; padding-top:35px; background-size:100%">{QR_CODE}</div>
   </div>
</div>

</body>

