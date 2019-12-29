
#from prompt import Prompt
import prompt


term = prompt.Prompt()
term.write('l')
print(term.readStdout(), end='')
print(term.readStderr(), end='')
