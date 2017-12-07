

//直接给  <select>下拉框  一个   onchange="_jumpMenu('parent',this,0)"  改变事件 直接跳转
function _jumpMenu(targ,selObj,restore)
{
    eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
    if (restore) selObj.selectedIndex=0;
}


//给 查询按钮 一个  onclick="_designated()"  点击事件
// function _designated(){
$('.js-print1').click(function(){
    var self=document.getElementById("logisticsordersearch-membercityname").value;
        // alert(self);
    var time = document.getElementById("logisticsordersearch-add_time").value;
        // alert(time);
    var  RandomUrl  = window.location.href.split("backend")[0];
        // alert(RandomUrl);exit();


               /* if (self !== "哈尔滨市"||"沈阳市")
                {
                    window.location.href="http://localhost/wl/backend/web/index.php?r=teller/heilongjiang";
                }*/

    switch(self)
    {
        case "沈阳市":
                window.location.href=RandomUrl+"backend/web/index.php?r=teller/nationwide&LogisticsOrderSearch%5Badd_time%5D="+time/*+"&LogisticsOrderSearch%5Ba%5D="+self*/; //在同当前窗口中打开窗口
            break;
        case "哈尔滨市":
                window.location.href=RandomUrl+"backend/web/index.php?r=teller/heilongjiang&LogisticsOrderSearch%5Badd_time%5D="+time/*+"&LogisticsOrderSearch%5Ba%5D="+self*/;
            break;
        case "大连市":
                window.location.href=RandomUrl+"backend/web/index.php?r=teller/dalian&LogisticsOrderSearch%5Badd_time%5D="+time/*+"&LogisticsOrderSearch%5Ba%5D="+self*/;
            break;
        default:
                window.location.href=RandomUrl+"backend/web/index.php?r=teller/nationwide&LogisticsOrderSearch%5Badd_time%5D="+time/*+"&LogisticsOrderSearch%5Ba%5D="+self*/;
    }
}
);