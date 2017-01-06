<?php
require 'SmsClient.php';

?>

<html>
<head>
    <style>
        .error {
            color: #FF0000;
        }

        .formfield * {
            vertical-align: top;
        }
    </style>
    <title>
        <?php echo "Dragonfly SMS Toolkit"; ?>
    </title>
</head>
<body>
    <h1>Dragonfly SMS Toolkit v1.0</h1>
    <form action="index.php" method="get">
        <table style="width: 100%">
            <tr>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            <tr>
                <td valign="top" style="width: 40%">
                    <fieldset>
                        <legend>Login Details:
                        </legend>
                        <label>
                            Username:
                            <input type="text" name="name" id="name" required="required" value="<?php echo isset($_GET['name']) ? $_GET['name'] : '' ?>" />
                        </label>
                        <br />
                        <br />
                        <label>
                            Password:
                            <input type="password" name="password" id="password" required="required" value="<?php echo isset($_GET['password']) ? $_GET['password'] : '' ?>" />
                        </label>
                        <br />
                    </fieldset>
                    <br />
                    <?php

                    function populateMessage($sourceErr,$destinationErr,$sourceIdErr,$messageErr){

                        $source = $_GET['source'];
                        $destination = $_GET['destination'];
                        $message = $_GET['message'];
                        $sourceId = $_GET['sourceId'];
                        if ($_GET['deliveryReceipt'] == 'on')
                        {
                            $deliveryReceipt = 'checked';
                        }
                        else
                        {
                        	$deliveryReceipt = '';
                        }


                        echo "<fieldset>
                        <legend>
                            Send Message:
                        </legend>
                        <label>
                            Source:
                            <input type='text' name='source' id='source' value='$source' />
                            <span class='error'>
                                $sourceErr
                            </span>
                        </label>
                        <br />
                        <br />
                        <label>
                            Destination:
                            <input type='text' name='destination' id='destination' value='$destination' />
                            <span class='error'>
                                $destinationErr
                            </span>
                        </label>
                        <br />
                        <br />
                        <label class=formfield>
                            Text:
                            <textarea rows='4' cols='40' name='message' id='message'>$message</textarea>
                            <span class='error'>
                                $messageErr
                            </span>
                        </label>
                        <br />
                        <br />
                        <label>
                            Source Id:
                            <input type='text' name='sourceId' id='sourceId' value='$sourceId'/>
                            <span class='error'>
                                $sourceIdErr
                            </span>
                        </label>
                        <br />
                        <br />
                        <label>
                            Delivery Receipt:
                            <input type='checkbox' name='deliveryReceipt' $deliveryReceipt/>
                        </label>
                        <br />
                        <br />
                        <input type='submit' value='Send Message' name='Send' />
                    </fieldset>
                    <br />";
                    }
                    ?>



                    <?php
                    function populateStatusResult($code,$subCode,$hasError,$errorMessage){

                        $code = isset($code) ? $code : $_GET['code'];
                        $subCode = isset($subCode) ? $subCode : $_GET['subCode'];
                        $hasError = isset($hasError) ? $hasError : $_GET['hasError'];
                        $errorMessage = isset($errorMessage) ? $errorMessage : $_GET['errorMessage'];

                        echo "<fieldset>
            <legend>
                Status Result:
            </legend>
            <label>
                Code:
                <input type='text' name='code' id='code' value='$code' disabled/>
                <input type='hidden' name='code' value='$code'/>
            </label>
            <br />
            <br />
            <label>
                SubCode:
                <input type='text' name='subCode' id='subCode' value='$subCode' disabled/>
                <input type='hidden' name='subCode' value='$subCode'/>
            </label>
            <br />
            <br />
            <label>
                Has Error:
                <input type='text' name='hasError' id='hasError' value='$hasError' disabled/>
                <input type='hidden' name='hasError' value='$hasError' />
            </label>
            <br />
            <br />
            <label>
                Error Message:
                <input type='text' name='errorMessage' id='errorMessage' value='$errorMessage' disabled/>
                <input type='hidden' name='errorMessage' value='$errorMessage' />
            </label>
            <br />
        </fieldset>
        <br/>";
                    }

                    function populateSendResult($messageId,$messageBalance,$pending,$surcharge){

                        $messageId = isset($messageId) ? $messageId : $_GET['messageId'];
                        $messageBalance = isset($messageBalance) ? $messageBalance : $_GET['messageBalance'];
                        $pending = isset($pending) ? $pending : $_GET['pendingMessages'];
                        $surcharge = isset($surcharge) ? $surcharge : $_GET['surchargeBalance'];

                        echo "<fieldset>
            <legend>
                Send Result:
            </legend>
            <label>
                Message Id:
                <input type='text' name='messageId' id='messageId' value='$messageId' disabled/>
                <input type='hidden' name='messageId' value='$messageId' />
            </label>
            <br />
            <br />
            <label>
                Message Balance:
                <input type='text' name='messageBalance' id='messageBalance' value='$messageBalance' disabled/>
                <input type='hidden' name='messageBalance' value='$messageBalance' />
            </label>
            <br />
            <br />
            <label>
                Pending Messages:
                <input type='text' name='pendingMessages' id='pendingMessages' value='$pending' disabled/>
                <input type='hidden' name='pendingMessages' value='$pending' />
            </label>
            <br />
            <br />
            <label>
                Surcharge Balance:
                <input type='text' name='surchargeBalance' id='surchargeBalance'  value='$surcharge' disabled/>
                <input type='hidden' name='surchargeBalance'  value='$surcharge' />
            </label>
            <br />
        </fieldset>
        <br />";
                    }

                    function checkMessageFields(){
                        $error = false;
                        // define variables and set to empty values
                        $sourceErr = $destinationErr = $messageErr = $sourceIdErr = "";

                        if(empty($_GET["source"]))
                        {
                            $sourceErr = "Source is required";
                            $error = true;
                        }
                        if(empty($_GET["destination"]))
                        {
                            $destinationErr = "Destination is required";
                            $error = true;
                        }
                        if($_GET["sourceId"] === null or !is_numeric($_GET["sourceId"]))
                        {
                            $sourceIdErr = "SourceId is required";
                            $error = true;
                        }
                        if(empty($_GET["message"]))
                        {
                            $messageErr = "Message is required";
                            $error = true;
                        }

                        return array($error,$sourceErr,$destinationErr,$sourceIdErr,$messageErr);
                    }

                    if(isset($_REQUEST['Send'])){

                        $newArr = checkMessageFields();

                        $errored = $newArr[0];
                        $sourceErr = $newArr[1];
                        $destinationErr = $newArr[2];
                        $sourceIdErr = $newArr[3];
                        $messageErr = $newArr[4];


                        if ($errored == true)
                        {
                            populateMessage($sourceErr,$destinationErr,$sourceIdErr,$messageErr);

                            $code = "";
                            $subCode = "";
                            $hasError = "";
                            $errorMessage  = "";

                            $messageId = "";
                            $messageBalance = "";
                            $pending = "";
                            $surcharge = "";

                            populateStatusResult($code,$subCode,$hasError,$errorMessage);
                            populateSendResult($messageId,$messageBalance,$pending,$surcharge);
                        }
                        else
                        {
                        	$client = new MScience\SmsClient($_GET["name"],$_GET["password"]);
                            $result2 = $client->send(array(new MScience\SmsMessage($_GET["source"],   //source
                                                                 $_GET["destination"],//destination number
                                                                 $_GET["sourceId"],             //source Id
                                                                 $_GET["message"]  //message
                                                                 ,$_GET["deliveryReceipt"])));      //delivery receipt


                            populateMessage($sourceErr,$destinationErr,$sourceIdErr,$messageErr);

                            if (!empty($result2[0]->ErrorMessage))
                            {
                                echo "Error Message: " .$result2[0]->ErrorMessage. "<br>";
                            }
                            else
                            {
                                $status = $client->getMessageStatus(array($result2[0]->MessageId));

                                $code = $status[0]->Code;
                                $subCode = $status[0]->SubCode;
                                $hasError = $status[0]->HasError() ? 'true' : 'false';
                                $errorMessage = $status[0]->ErrorMessage();

                                populateStatusResult($code,$subCode,$hasError,$errorMessage);

                                $messageId = $result2[0]->MessageId;
                                $messageBalance = $result2[0]->MessageBalance;
                                $pending = $result2[0]->PendingMessages;
                                $surcharge = $result2[0]->SurchargeBalance;

                                populateSendResult($messageId,$messageBalance,$pending,$surcharge);

                            }
                        }
                    }
                    else
                    {
                        $sourceErr = "";
                        $destinationErr = "";
                        $sourceIdErr = "";
                        $messageErr = "";
                        populateMessage($sourceErr,$destinationErr,$sourceIdErr,$messageErr);

                        $code = $_GET['code'];
                        $subCode = $_GET['subCode'];
                        $hasError = $_GET['hasError'];
                        $errorMessage  = $_GET['errorMessage'];

                        $messageId = $_GET['messageId'];
                        $messageBalance = $_GET['messageBalance'];
                        $pending = $_GET['pendingMessages'];
                        $surcharge = $_GET['surchargeBalance'];

                        populateStatusResult($code,$subCode,$hasError,$errorMessage);
                        populateSendResult($messageId,$messageBalance,$pending,$surcharge);
                    }

                    ?>
                </td>
                <td valign="top" style="width: 40%">
                    <?php
                    function populateDeliveryReceipts($dRCode,$dRDeliveryReceipt,$dRDestination,$dRErrorMessage,$dRHasError,$dRMessageId,$dRReceived,$dRSource,$dRSourceId,$dRText){
                        echo "<fieldset>
            <legend id='dRMessageId'>
                Message Id: $dRMessageId
            </legend>
            <label>
                Code:
                <input type='text' name='dRCode' id='dRCode' value='$dRCode' disabled/>
            </label>
            <br />
            <br />
            <label>
                Source:
                <input type='text' name='dRSource' id='dRSource' value='$dRSource' disabled/>
            </label>
            <br />
            <br />
            <label>
                Destination:
                <input type='text' name='dRDestination' id='dRDestination' value='$dRDestination' disabled/>
            </label>
            <br />
            <br />
<label>
                Received:
                <input type='text' name='dRReceived' id='dRReceived' value='$dRReceived' disabled/>
            </label>
            <br />
            <br />
<label>
                Source Id:
                <input type='text' name='dRSourceId' id='dRSourceId' value='$dRSourceId' disabled/>
            </label>
            <br />
            <br />
<label>
                Delivery Reciept:
                <input type='text' name='dRDeliveryReceipt' id='dRDeliveryReceipt' value='$dRDeliveryReceipt' disabled/>
            </label>
            <br />
            <br />
<label class=formfield >
                Text:
<textarea rows='4' cols='40' name='dRText' id='dRText' disabled >$dRText</textarea>
            </label>

            <br />
            <br />
<label>
                Has Error:
                <input type='text' name='dRHasError' id='dRHasError' value='$dRHasError' disabled/>
            </label>
            <br />
            <br />
<label>
                Error Message:
                <input type='text' name='dRErrorMessage' id='dRErrorMessage' value='$dRErrorMessage' disabled/>
            </label>
            <br />
        <br/>
        </fieldset>                          
        <br/>
        <input type='submit' value='Recieve Inbound/DLR' name='RecieveDLR' />";
                    }

                    if(isset($_REQUEST['RecieveDLR'])){

                        $client = new MScience\SmsClient($_GET["name"],$_GET["password"]);

                        $deliveryReceipts = $client->getDeliveryReceipts();

                        $inboundMessages = $client->getInboundMessages();

                        $inboundResults = array_merge($deliveryReceipts, $inboundMessages);

                        $messageCount = count($inboundResults);
                        if (count($inboundResults) > 0)
                        {

                            echo "<div id='deliverydiv'>
                    <fieldset>
                <legend>
                Inbound Messages/Delivery Receipts:
            </legend>";
                            foreach ($inboundResults as $inboundResult)
                            {
                                $dRCode = $inboundResult->Code;
                                $dRDeliveryReceipt = $inboundResult->DeliveryReceipt;
                                $dRDestination = $inboundResult->Destination;
                                $dRErrorMessage = $inboundResult->ErrorMessage;
                                $dRHasError = $inboundResult->HasError;
                                $dRMessageId = $inboundResult->Id;
                                $dRReceived = $inboundResult->Received;
                                $dRSource = $inboundResult->Source;
                                $dRSourceId = $inboundResult->SourceId;
                                $dRText = $inboundResult->Text;

                                populateDeliveryReceipts($dRCode,$dRDeliveryReceipt,$dRDestination,$dRErrorMessage,$dRHasError,$dRMessageId,$dRReceived,$dRSource,$dRSourceId,$dRText);

                            }
                            echo "</fieldset>
                                    </div>
                                    <br/>";
                        }
                        else
                        {
                        	$dRCode = "";
                            $dRDeliveryReceipt = "";
                            $dRDestination = "";
                            $dRErrorMessage = "";
                            $dRHasError = "";
                            $dRMessageId = "";
                            $dRReceived = "";
                            $dRSource = "";
                            $dRSourceId = "";
                            $dRText = "";
                            echo "<div id='deliverydiv'>
                        <fieldset>
                        <legend>
                        Inbound Messages/Delivery Receipts:
                        </legend>";

                            populateDeliveryReceipts($dRCode,$dRDeliveryReceipt,$dRDestination,$dRErrorMessage,$dRHasError,$dRMessageId,$dRReceived,$dRSource,$dRSourceId,$dRText);

                            echo "</fieldset>
                                  </div>
                                <br/>";

                        }

                    }
                    else
                    {
                        $dRCode = "";
                        $dRDeliveryReceipt = "";
                        $dRDestination = "";
                        $dRErrorMessage = "";
                        $dRHasError = "";
                        $dRMessageId = "";
                        $dRReceived = "";
                        $dRSource = "";
                        $dRSourceId = "";
                        $dRText = "";
                        echo "
<div id='deliverydiv'>
<fieldset>
                        <legend>
                        Inbound Messages/Delivery Receipts:
                        </legend>";

                        populateDeliveryReceipts($dRCode,$dRDeliveryReceipt,$dRDestination,$dRErrorMessage,$dRHasError,$dRMessageId,$dRReceived,$dRSource,$dRSourceId,$dRText);

                        echo "</fieldset>
</div>
                                <br/>";
                    }

                    populateInboundMessagesCount($messageCount);

                    ?>
                </td>


                <?php

                function populateInboundMessagesCount($messageCount){
                    echo "<td valign='top' style='width:20%'>
                        <div align='center'>
                        <img src='/Images/image004.png' title='Logo of a company' alt='Logo of a company' />
                        </div>
                        <br/>
                        <br/>
                        <label>
                        Number of Inbound Messages
                        <input type='text' name='messageCount' id='messageCount' value='$messageCount' disabled />
                        </label>
                        <br/>
                        <br/>
                        
<br/>
<input type='button' name='Clear' value='Clear' id='btnReset' onclick='ClearFields();'/>
        <br/>
                        </td>";
                }


                ?>


            </tr>
        </table>

    </form>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script type="text/javascript">
    function ClearFields() {

        document.getElementById("source").value = "";
        document.getElementById("destination").value = "";
        document.getElementById("message").value = "";
        document.getElementById("sourceId").value = "";
        document.getElementById("code").value = "";
        document.getElementById("subCode").value = "";
        document.getElementById("hasError").value = "";
        document.getElementById("errorMessage").value = "";
        document.getElementById("messageId").value = "";
        document.getElementById("messageBalance").value = "";
        document.getElementById("pendingMessages").value = "";
        document.getElementById("surchargeBalance").value = "";
        document.getElementById("messageCount").value = "";

        $("#deliverydiv").empty();
        
        var deliveryTxt = "<fieldset><legend>Inbound Messages/Delivery Receipts:</legend><fieldset><legend id='dRMessageId'>Message Id: </legend><label>Code: <input type='text' name='dRCode' id='dRCode' value='' disabled/>"
        + "</label><br /><br /><label>Source: <input type='text' name='dRSource' id='dRSource' value='' disabled/></label><br /><br /><label>Destination: "
        + "<input type='text' name='dRDestination' id='dRDestination' value='' disabled/></label><br /><br /><label>Received: <input type='text' name='dRReceived' id='dRReceived' value='' disabled/>"
        + "</label><br /><br /><label>Source Id: <input type='text' name='dRSourceId' id='dRSourceId' value='' disabled/></label><br /><br /><label>Delivery Reciept: "
        + "<input type='text' name='dRDeliveryReceipt' id='dRDeliveryReceipt' value='' disabled/></label><br /><br /><label class=formfield >Text: "
        + "<textarea rows='4' cols='40' name='dRText' id='dRText' disabled ></textarea></label><br /><br /><label>Has Error: "
        + "<input type='text' name='dRHasError' id='dRHasError' value='' disabled/></label><br /><br /><label>Error Message: "
        + "<input type='text' name='dRErrorMessage' id='dRErrorMessage' value='' disabled/></label><br /><br/></fieldset><br /><input type='submit' value='Recieve Inbound/DLR' name='RecieveDLR' />"
        + "</fieldset></div>";

        $("#deliverydiv").append(deliveryTxt);

        
    }
</script>
</html>




