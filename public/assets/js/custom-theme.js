var assignmentListUrl = "https://tracker.wavyinformatics.com/assignment_list";


var assignmentListUrl = "{{ route('assignment_list') }}";



/* ====== Self Attendence Blade file ======== */
/*
setInterval(updateClock, 1000);

function updateClock() {
    const now = new Date();
    const tenhour = Math.floor(now.getHours() / 10);
    const hour = now.getHours() % 10;
    const tenmin = Math.floor(now.getMinutes() / 10);
    const min = now.getMinutes() % 10;
    const tensec = Math.floor(now.getSeconds() / 10);
    const sec = now.getSeconds() % 10;
    const ampm = now.getHours() >= 12 ? 'P' : 'A';
    document.querySelector('.tenhour .base').textContent = tenhour;
    document.querySelector('.hour .base').textContent = hour;
    document.querySelector('.tenmin .base').textContent = tenmin;
    document.querySelector('.min .base').textContent = min;
    document.querySelector('.tensec .base').textContent = tensec;
    document.querySelector('.sec .base').textContent = sec;
    document.querySelector('.ampm .base').textContent = ampm;
}


function OfficeClockInOut(action) {
    Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to ${action.replace('-', ' ')}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, do it!',
        cancelButtonText: 'No, cancel!',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/attendance/office`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        action
                    })
                }).then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire('Success!', data.message, 'success').then(() => {
                        location.reload(); // Reload the page after showing the success message
                    });
                })
                .catch(error => {
                    Swal.fire('Error!', 'There has been a problem with your fetch operation: ' + error,
                        'error');
                });
        }
    });
    return false;
}

function LunchClockInOut(action) {
    Swal.fire({
        title: 'Are you sure?',
        text: `Do you want to ${action.replace('-', ' ')}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, do it!',
        cancelButtonText: 'No, cancel!',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/attendance/lunch`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        action
                    })
                }).then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    Swal.fire('Success!', data.message, 'success').then(() => {
                        location.reload(); // Reload the page after showing the success message
                    });
                })
                .catch(error => {
                    Swal.fire('Error!', 'There has been a problem with your fetch operation: ' + error,
                        'error');
                });
        }
    });
    return false;
}

function handleBreak(action) {
    let confirmationText, successMessage;

    if (action === 'break') {
        confirmationText = 'Do you want to take a break?';
        successMessage = 'Break Taken. Enjoy your break!';
    } else if (action === 'stop') {
        confirmationText = 'Do you want to stop the break?';
        successMessage = 'Break Stopped. Welcome back!';
    }

    Swal.fire({
        title: 'Are you sure?',
        text: confirmationText,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: action === 'break' ? 'Yes, take a break' : 'Yes, stop break',
        cancelButtonText: 'No, cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/attendance/breaktime`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    action
                })
            }).then(response => {
                if (!response.ok) {
                    // throw new Error('Network response was not ok ' + response.statusText);
                    throw new Error('Please first start Office In');
                }
                return response.json();
            })
            .then(data => {
                Swal.fire(successMessage, '', 'success').then(() => {
                    location.reload(); // Reload the page after showing the success message
                
                });
            })
            .catch(error => {
                Swal.fire('Error!', 'There has been a problem with your fetch operation: ' + error, 'error');
            });
        }
    });
}*/

/*================== Dashboard Blade File =============== */


/* ================= Project Blade file ================= */



async function ajaxCall(data, post_type, url) {
    try {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            }
        });

        const response = await $.ajax({
            type: post_type,
            url: url,
            data: JSON.stringify(data),
            contentType: 'application/json',
            dataType: 'json'
        });

        // showToast(response.message, 'success', 'bottom-right');

        return response;
    } catch (xhr) {
        let statusCode = xhr.status; // Get status code
        let msg = '';
        console.log("xhr is", xhr);

        let data = '';

        if (statusCode === 500) {
            msg = 'Internal server error!';
        } else if (statusCode === 422) {
            msg = JSON.parse(xhr.responseText).message || 'An unknown error occurred.';
            data = JSON.parse(xhr.responseText).errors;
        } else {
            try {
                msg = JSON.parse(xhr.responseText).message || 'An unknown error occurred.';
            } catch (e) {
                msg = 'An unknown error occurred.';
            }
        }

        // showToast(msg, 'error', 'bottom-right');

        return {
            statusCode: statusCode,
            message: msg,
            errors: data,
            success: false
        };
    }
}

const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))