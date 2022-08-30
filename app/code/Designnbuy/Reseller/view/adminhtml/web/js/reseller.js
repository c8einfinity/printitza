
define([
"jquery",
], function(jQuery){
   "use strict";
   (function(e){ 
    Validation.add
        ('validate-lowercase','Input must be inuppercase',function(v){
            return Validation.get('IsEmpty').test(v) || (!/^[a-z]+$/.test(v));
        });
    });
});