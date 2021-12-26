function redirect_to_main(){
    document.cookie = "error=; expires= Thu, 01 Jan 1970 00:00:00 GMT";
    window.location.replace('/');
}