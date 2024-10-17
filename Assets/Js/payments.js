function paymentGateway(paymentData, paymentStatusCallback) {
    var xhttp = new XMLHttpRequest();

    // When the state changes, check if the response is ready
    xhttp.onreadystatechange = () => {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            var obj = JSON.parse(xhttp.responseText);

            if (obj.success) {
                if (!paymentData) {
                    console.error("Payment data is missing");
                    swal("Error", "Payment data is missing. Please try again.", "error");
                    return;
                }

                // Debugging log to check if rideID is present in paymentData
                console.log("Payment data in paymentGateway:", paymentData);
                console.log("Payment amount received in Payments.js: " + obj.paymentData.amount);

                // PayHere payment handlers
                payhere.onCompleted = function onCompleted(orderId) {
                    console.log("Payment completed. OrderID: " + orderId);
                    console.log("rideID in paymentData: ", paymentData.rideID);  // Log rideID here
                
                    sendPaymentStatusToServer(orderId, "completed");
                
                    // Call the callback function to handle success
                    paymentStatusCallback("completed", orderId, paymentData.rideID, paymentData.driverID, paymentData.amount);
                };                
                
                payhere.onDismissed = function onDismissed() {
                    console.log("Payment dismissed");
                    sendPaymentStatusToServer(null, "dismissed");

                    // Call the callback function to handle dismissed payment
                    paymentStatusCallback("dismissed", null, paymentData.rideID, paymentData.driverID, paymentData.amount);
                };

                payhere.onError = function onError(error) {
                    console.log("Payment error: " + error);
                    sendPaymentStatusToServer(null, "error");

                    // Call the callback function to handle payment error
                    paymentStatusCallback("error", null, paymentData.rideID, paymentData.driverID, paymentData.amount);
                };

                // Prepare the payment object using the response from Payments.php
                var payment = {
                    "sandbox": true,  // Change to false in live environment
                    "merchant_id": paymentData.merchant_id,
                    "return_url": "http://localhost/payhere_config/",
                    "cancel_url": "http://localhost/payhere_config/",
                    "notify_url": "http://sample.com/notify",
                    "order_id": paymentData.order_id,
                    "items": "Ride Payment",
                    "amount": paymentData.amount,
                    "currency": paymentData.currency,
                    "hash": paymentData.hash,
                    "first_name": paymentData.first_name,
                    "last_name": paymentData.last_name,
                    "email": paymentData.email,
                    "phone": paymentData.phone,
                    "address": paymentData.address,
                    "city": paymentData.city || "Colombo",
                    "country": "Sri Lanka",
                    "delivery_city": paymentData.city || "Colombo",
                    "delivery_country": "Sri Lanka"
                };

                // Start the payment process
                payhere.startPayment(payment);
            } else {
                console.error("Error in preparing payment:", obj.message);
                swal("Error", "There was an error preparing your payment. Please try again.", "error");
            }
        }
    };

    // Send a POST request to Payments.php
    xhttp.open("POST", "/CityTaxi/Functions/Common/Payments.php", true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send(JSON.stringify(paymentData));
}


// Function to send the payment status to the server
function sendPaymentStatusToServer(orderId, status) {
    return fetch('/CityTaxi/Functions/Common/Payments.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            orderId: orderId,
            paymentStatus: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            swal("Error", "Error updating payment status: " + data.message, "error");
            throw new Error(data.message);  // Reject the Promise if the status update fails
        }
        console.log("Payment status updated on the server: " + status);
    })
    .catch(error => {
        console.error("Error sending payment status:", error);
        swal("Error", "There was an error sending payment status. Please try again.", "error");
        throw new Error("Error sending payment status");
    });
}