(function() {
    // ตั้งเวลา Timeout (1 ชั่วโมง = 60 นาที * 60 วินาที * 1000 มิลลิวินาที)
    //const timeoutDuration = 3600000; // 1 ชั่วโมง
    //const timeoutDuration = 10000; // 10 วินาที 
    const timeoutDuration = 60 * 60 * 1000; // 1 ชั่วโมงแบบอ่านง่าย

    let timeoutTimer;

    function startTimer() {
        clearTimeout(timeoutTimer);
        timeoutTimer = setTimeout(doLogout, timeoutDuration);
    }

    function doLogout() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'หมดเวลาการเชื่อมต่อ',
                text: 'คุณไม่ได้ทำรายการใดๆ เป็นเวลานาน ระบบจะออกจากระบบอัตโนมัติ',
                icon: 'warning',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                willClose: () => {
                     window.location.href = '../logout.php'; 
                }
            });
        } else {
             alert("หมดเวลาการเชื่อมต่อ กรุณาเข้าสู่ระบบใหม่");
             window.location.href = '../logout.php';
        }
    }

    const events = ['mousemove', 'keypress', 'scroll', 'click', 'touchstart'];

    events.forEach(event => {
        document.addEventListener(event, startTimer);
    });

    startTimer();
})();