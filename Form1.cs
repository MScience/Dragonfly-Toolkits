using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Globalization;
using System.IO;
using System.Linq;
using System.Threading;
using System.Windows.Forms;
using MScience.Sms;

namespace DragonflySMSToolkit
{

    public partial class ToolboxForm : Form
    {
        private InboundMessageResult[] _deliveryReceipts;
        private InboundMessageResult[] _inboundMessages;

        private int _stepCounter;
        private const string noInboundMessages = "No inbound messages. ";
        private const string noDlrMessages = "No delivery receipt messages. ";


        public ToolboxForm()
        {
            InitializeComponent();
            devInbPosBox.Text = "";
            devStepInboundButton.Hide();
            devInbPosBox.Hide();
        }

        private bool CheckCredentials(string accountId, string password)
        {
            bool credentialsPassed = true;

            if (String.IsNullOrEmpty(accountId))
            {
                lblAccountIdReq.Text = @"*Account Id is required";
                credentialsPassed = false;
            }
            if (String.IsNullOrEmpty(password))
            {
                lblPasswordReq.Text = @"*Password is required";
                credentialsPassed = false;
            }

            return credentialsPassed;
        }

        private void SendButton_Click(object sender, EventArgs e)
        {
            bool acceptedCredentials = CheckCredentials(accountTextBox.Text, PasswordTextBox.Text);

            if (!acceptedCredentials)
            {
                return;
            }
            
            var client = new SmsClient { AccountId = accountTextBox.Text, Password = PasswordTextBox.Text };
            
            int sourceId;
            bool dlrReport;

            ClearStatusPanel();
            ClearSendResultPanel();

            if (Int32.TryParse(sndSourceIdBox.Text, out sourceId))
                Console.WriteLine(@"String could not be parsed.");
            if (dlrCheckBox.Checked)
                dlrReport = true;
            else
                dlrReport = false;

            var smsmsg = new SmsMessage
            {
                Source = sndSourceBox.Text,
                Text = sndTextBox.Text,
                DeliveryReport = dlrReport,
                SourceId = sourceId,
                Destination = sndDestinationBox.Text
            };


            //   SEND MULTIPLE MESSAGE
            var messages = new List<SmsMessage> {smsmsg};


            /*new SmsMessage
               {
                Source = sndSourceBox.Text,
                Text = sndTextBox.Text,
                DeliveryReport = dlrReport,
                SourceId = sourceId,
                Destination = sndDestinationBox.Text
               });
            */

            var result = client.Send(messages.ToArray());
            Thread.Sleep(500);
            result.ToList().ForEach(r => WriteSendResult(r, client));
            //WriteSendResult(result.First(), client);
            /*
            destination: destination phone number
            source: source phone number. Use an empty string if no source number is available
            message: the message to send
            sourceId: your own Id for the message. This can be used to correlate delivery receipts to messages sent
            deliveryReceipt: pass true when a delivery receipt is required
            */
        }

        private void ReceiveButton_Click(object sender, EventArgs e)
        {
            _stepCounter = 0;
            bool acceptedCredentials = CheckCredentials(accountTextBox.Text, PasswordTextBox.Text);

            if (!acceptedCredentials)
            {
                return;
            }

            ClearInboundPanel();
            var client = new SmsClient { AccountId = accountTextBox.Text, Password = PasswordTextBox.Text };
            
            _deliveryReceipts = client.GetDeliveryReceipts();
            _inboundMessages = client.GetInboundMessages();

            if (_inboundMessages.Length == 0)
            {
                devLogTextBox.Text = noInboundMessages;
            }

            _deliveryReceipts = _deliveryReceipts.Concat(_inboundMessages).ToArray();

            if (_deliveryReceipts.Length > 0)
            {
                Console.WriteLine(@"Delivery Receipts:");
                
                if (_deliveryReceipts.Length > 1)
                {
                    devStepInboundButton.Show();
                    devInbPosBox.Show();
                }
                CompleteReceivePanel(ref _deliveryReceipts[_stepCounter]);
            }
            else
            {
                devLogTextBox.Text = devLogTextBox.Text + noDlrMessages;
            }
                devNoInboundMsgBox.Text = _deliveryReceipts.Length.ToString();
        }


        private void WriteSendResult(SendResult sendResult, ISmsClient client)
        {
            Console.WriteLine(sendResult.Code);
            if (sendResult.HasError)
            {
                statusrCodeBox.Text = sendResult.Code;
                statusrHasErrortextBox.Text = sendResult.HasError ? "True" : "False";
                statusrErrorMessagetextBox.Text = sendResult.ErrorMessage;
                Console.WriteLine(sendResult.ErrorMessage);
            }
            else
            {
                Console.WriteLine(@"Message Id {0}", sendResult.MessageId);
                Console.WriteLine(@"Balance {0}", sendResult.MessageBalance);
                Console.WriteLine(@"Pending : {0}", sendResult.PendingMessages);
                Console.WriteLine(@"Surcharge : {0}", sendResult.SurchargeBalance);
                var statusResult = client.GetMessageStatus(new[] { sendResult.MessageId });
                Console.WriteLine(@"Message Status Code = {0}, Status = {1}", statusResult.First().Code, statusResult.First().Status);
                Console.WriteLine(@"Message HasError = {0}, ErrorMessage = {1}", statusResult.First().HasError ? "True" : "False", statusResult.First().ErrorMessage);

                srMessageIdBox.Text = sendResult.MessageId.ToString();
                srMessageBalanceBox.Text = sendResult.MessageBalance.ToString();
                srPendingBox.Text = sendResult.PendingMessages.ToString();
                srSurchargeBox.Text = sendResult.SurchargeBalance.ToString(CultureInfo.InvariantCulture);
                srHasErrorBox.Text = sendResult.HasError.ToString();
                srErrorMessageBox.Text = sendResult.ErrorMessage ?? "";

                statusResult = client.GetMessageStatus(new[] { sendResult.MessageId });
                statusrCodeBox.Text = statusResult.First().Code;
                statusrSubCodeBox.Text = statusResult.First().Status;
                statusrHasErrortextBox.Text = statusResult.First().HasError ? "True" : "False";
                statusrErrorMessagetextBox.Text = statusResult.First().ErrorMessage;
            }
        }


        private void CompleteReceivePanel(ref InboundMessageResult inboundMessage)
        {
            recCodeBox.Text = inboundMessage.Code;
            recIdBox.Text = inboundMessage.Id.ToString();
            recDeliveryReceiptBox.Text = inboundMessage.DeliveryReceipt.ToString();
            recDestinationBox.Text = inboundMessage.Destination;
            recReceivedDateTimeBox.Text = inboundMessage.Received.ToString(CultureInfo.InvariantCulture);
            recSourceBox.Text = inboundMessage.Source;
            recHasErrorBox.Text = inboundMessage.HasError.ToString();
            recSourceId.Text = inboundMessage.SourceId.ToString();
            recTextBox.Text = inboundMessage.Text;
        }


        private void devStepInboundButton_Click(object sender, EventArgs e)
        {
            Button btnSender = (Button)sender;
            if (btnSender == devStepInboundButton)
            {
                if (_deliveryReceipts != null)
                {
                    if (_deliveryReceipts.Length > 0)
                    {
                        CompleteReceivePanel(ref _deliveryReceipts[_stepCounter]);
                        devInbPosBox.Text = (_stepCounter + 1).ToString();
                        _stepCounter = _stepCounter + 1 >= _deliveryReceipts.Length ? 0 : ++_stepCounter;
                    }
                }
            }
        }

        private void ClearSendMessagePanel()
        {
            sndSourceBox.Text = "";
            sndTextBox.Text = "";
            dlrCheckBox.Checked = false;
            sndSourceIdBox.Text = "";
            sndDestinationBox.Text = "";
        }

        private void ClearInboundArrays()
        {
            _deliveryReceipts = null;
            _inboundMessages = null;
        }

        private void ClearInboundPanel()
        {
            recCodeBox.Text = "";
            recIdBox.Text = "";
            recDeliveryReceiptBox.Text = "";
            recDestinationBox.Text = "";
            recSourceBox.Text = "";
            recDestinationBox.Text = "";
            recSourceBox.Text = "";
            recReceivedDateTimeBox.Text = "";
            recHasErrorBox.Text = "";
            recSourceId.Text = "";
            recTextBox.Text = "";
            devNoInboundMsgBox.Text = "";
        }
        private void ClearSendResultPanel()
        {
            srMessageIdBox.Text = "";
            srMessageBalanceBox.Text = "";
            srPendingBox.Text = "";
            srSurchargeBox.Text = "";
            srHasErrorBox.Text = "";
            srErrorMessageBox.Text = "";
        }

        private void ClearStatusPanel()
        {
            devInbPosBox.Hide();
            devStepInboundButton.Hide();

            statusrHasErrortextBox.Text = "";
            statusrCodeBox.Text = "";
            statusrSubCodeBox.Text = "";

            devNoInboundMsgBox.Text = "";
            devNoInboundMsgBox.Text = "";

            devInbPosBox.Text = "";
            devLogTextBox.Text = "";
        }

        private void clearButton_Click(object sender, EventArgs e)
        {
            ClearSendMessagePanel();
            ClearInboundPanel();
            ClearSendResultPanel();
            ClearStatusPanel();
            ClearInboundArrays();
        }


        private void ToolboxForm_Load(object sender, EventArgs e)
        {

        }

        private void panel6_Paint(object sender, PaintEventArgs e)
        {

        }

        
    }
}
