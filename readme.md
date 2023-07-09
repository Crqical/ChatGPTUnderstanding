 

Hello! This project is about the connection between Google's Framework and Chat GPT's API.

On Line  51 of sheetgrade.php, we kindly ask you to add your own API key found here https://platform.openai.com/account/api-keys

$apiKey = "sk-";

All depencies can be found in the "vendor" file, as well as information about the Composer. 


Explanation:

Index.php - Login process through Googles Framework starts. 02Auth starts the process of connecting to the google account and retrieving all Google Sheets/Documents File.

oath2callback.php - Google's Framework to collect all the data of the user and allow the website to collect the data after logging in with your google account.

sheetss.php - Prompt / Question / Data

Sheegrade.php - Chat GPT Response to the data provided.

client.secrets.json - Do not touch for now, as this is the credientals Google uses to determine where the data is going to and to who.

chat_gpt_response.json - Chat GPT's response saved in JSON.

Accounts with Access:
benrudtest@gmail.com / tbenrud@guhsd.net / toddbenrud@gmail.com

Please contact crqical1@gmail.com for more information, or to request access. 



Copyright (c) 2023 Eibil Yousibe

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
