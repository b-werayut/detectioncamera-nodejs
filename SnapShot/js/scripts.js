function nodate(){
    let paramnone = $('#paramnone').val();
    if(paramnone == ''){
      Swal.fire({
      position: "center",
      icon: "error",
      title: "กรุณากรอกวันที่!",
      showConfirmButton: true
    });
    }
  }
  

    function showimg(img){
      Swal.fire({
        imageUrl: img,
        imageWidth: 800,
        imageHeight: 500,
        width: 850,
      });
    }
