(function(_slides){
    each(_slides,function(_slide,i){
        var _ctrls = _slide.getElementsByTagName('i');
        var _lists = _slide.getElementsByTagName('li');
        each(_ctrls,function(_ctrl,i){
            _ctrl.onclick=function(){
                each(_lists,function(_list,i){
                    delClass(_list,"z-crt");
                });
                each(_ctrls,function(_ctrl,i){
                    delClass(_ctrl,"z-crt");
                });
                addClass(_lists[i],"z-crt");
                addClass(_ctrls[i],"z-crt");
            }
        });
    });
    function hasClass(_object,_clsname){
        var _clsname = _clsname.replace(".","");
        var _sCls = " "+(_object.className)+" ";
        return (_sCls.indexOf(" "+_clsname+" ") != -1) ? true : false;
    }
    function toClass(_str){
        var _str = _str.toString();
        _str = _str.replace(/(^\s*)|(\s*$)/g,"");
        _str = _str.replace(/\s{2,}/g," ");
        return _str;
    }
    function addClass(_object,_clsname){
        var _clsname = _clsname.replace(".","");
        if(!hasClass(_object,_clsname)){
            _object.className = toClass(_object.className+(" "+_clsname));
        }
    }
    function delClass(_object,_clsname){
        var _clsname = _clsname.replace(".","");
        if(hasClass(_object,_clsname)){
            _object.className = toClass(_object.className.replace(new RegExp("(?:^|\\s)"+_clsname+"(?=\\s|$)","g")," "));
        }
    }
    function each(_objects,_fn){
        for(var i=0,len=_objects.length;i<len;i++){
            _fn(_objects[i],i);
        }
    }
})(document.getElementsByTagName('div'));
var _indList = new Array();
_indList['new'] = "abc";
function getListInfo(_type) {
    if(_type=="") return false;
    var _tmp = '#'+_type;
    $("#newList").attr("class","");
    $("#visList").attr("class","");
    $("#scoList").attr("class","");
    $("#timList").attr("class","");
    if(_indList[_type]==null)
    {
       var _url = $(_tmp).attr("tmp");
       if(_url==null) return false;
       $.get(_url,function(data){
           if(data!=""){
                $("#_indexList").html(data);
                $(_tmp).attr("class","z-crt");
                _indList[_type] = data;
           }
       });
    }else
    {
        $("#_indexList").html(_indList[_type]);
        $(_tmp).attr("class","z-crt");
    }
}