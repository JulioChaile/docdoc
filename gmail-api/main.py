import pickle
import os.path
from googleapiclient.discovery import build
from google_auth_oauthlib.flow import InstalledAppFlow
from google.auth.transport.requests import Request
from flask import Flask, request
from email.mime.text import MIMEText
import base64

app = Flask(__name__)


# If modifying these scopes, delete the file token.pickle.
SCOPES = [
    'https://www.googleapis.com/auth/gmail.send'
]

# Quickstart code for gmail api
# https://developers.google.com/gmail/api/quickstart/python#step_3_set_up_the_sample


class Gmail:
    def __init__(self):
        """Shows basic usage of the Gmail API.
        Lists the user's Gmail labels.
        """
        creds = None
        # The file token.pickle stores the user's access and refresh tokens, and is
        # created automatically when the authorization flow completes for the first
        # time.
        if os.path.exists('token.pickle'):
            with open('token.pickle', 'rb') as token:
                creds = pickle.load(token)
        # If there are no (valid) credentials available, let the user log in.
        if not creds or not creds.valid:
            if creds and creds.expired and creds.refresh_token:
                creds.refresh(Request())
            else:
                flow = InstalledAppFlow.from_client_secrets_file(
                    'credentials.json', SCOPES)
                creds = flow.run_local_server(port=0)
            # Save the credentials for the next run
            with open('token.pickle', 'wb') as token:
                pickle.dump(creds, token)

        self.creds = creds

    def getService(self):
        return build('gmail', 'v1', credentials=self.creds)

    def create_message(self, sender, to, subject, message_text):
        message = MIMEText(message_text, 'html')
        message['to'] = to
        message['from'] = sender
        message['subject'] = subject
        raw_message = base64.urlsafe_b64encode(
            message.as_string().encode("utf-8"))
        return {
            'raw': raw_message.decode("utf-8")
        }

    def send_message(self, message):
        try:
            service = self.getService()
            message = service.users().messages().send(
                userId="me", body=self.create_message(**message)).execute()
            print('Message Id: %s' % message['id'])
            return message
        except Exception as e:
            msg = 'An error occurred: %s' % e
            print(msg)
            return {"error": msg}


@app.route('/send', methods=['POST'])
def send():
    data = request.json
    if 'token' not in data or data['token'] != 'AIzaSyDjyytt5GAgQoLohIeGaY5dJiHhoKu6ih0':
        return {"error": "not allowed"}
    data.pop('token')
    api = Gmail()
    print("request, send:", data)
    return api.send_message(data)


if __name__ == '__main__':
    app.run()
