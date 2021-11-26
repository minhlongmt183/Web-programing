
function fname_check()
{
    // alert('inhere');
    var x = document.sign_up.fname.value;
    if (x.length < 2 || x.length > 30){
        alert("First Name: chuỗi từ 2-30 kí tự");
        return false;
    }
    return true;
}

function lname_check()
{
    // alert('inhere');
    var x = document.sign_up.lname.value;
    if (x.length < 2 || x.length > 30){
        alert("Last Name: chuỗi từ 2-30 kí tự");
        // document.sign_up.lname.focus();
        return false;
    }
    return true;
}

function email_check()
{
  var re = /\S+@\S+\.\S+/;
  if(!re.test(document.sign_up.email.value)){
    alert("(email theo định dạng: <sth>@<sth>.<sth>");
    return false;
  }
  return true;
}

function pw_check()
{
    // alert('inhere');
    var x = document.sign_up.pwd.value;
    if (x.length < 2 || x.length > 30){
      alert("Password: chuỗi từ 2-30 kí tự");
      return false;
    }
    return true;
}

function validate(email){
  if(fname_check() && lname_check() && email_check() && pw_check()){
    alert("Complete!");
  }
  
}

function reset_btn(){
  document.sign_up.reset();
}