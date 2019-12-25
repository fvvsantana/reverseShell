import requests
import logging

def login(session, url, type):
    return session.request('GET', url, {'login':1, 'type':type})


def main():
    # Module for connection debugging
    logging.basicConfig(level=logging.DEBUG)

    # Server domain
    base_url = 'http://localhost/kellyclarkson/'

    # Store command
    data = {'cmd': None}

    # Start session
    with requests.session() as s:
        # Log in as attacker
        response = login(s, base_url + 'login.php', 'attacker')

        #print(s.cookies.get_dict())
        #print(response.content)
        print(response.text)

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
