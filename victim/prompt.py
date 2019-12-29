import subprocess

class Prompt:
    def __init__(self):
        command = '/bin/bash'
        #ON_POSIX = 'posix' in sys.builtin_module_names
        #command = 'echo ""'
        proc = subprocess.Popen(command.split(),
                                stdin=subprocess.PIPE,
                                stdout=subprocess.PIPE,
                                stderr=subprocess.PIPE,
                                bufsize=1,
                                universal_newlines=True)
                                #close_fds=ON_POSIX)
        self.__stdin = proc.stdin;
        self.__stdout = proc.stdout;
        self.__stderr = proc.stderr;

    def write(self, command):
        self.__stdin.write(command.rstrip() + ' && echo "\n#FIM\n" && >&2 echo "\n#FIM\n" \n')

    def readStdout(self):
        output = ''
        currentOutput = ''
        while(currentOutput != '#FIM\n'):
            currentOutput = self.__stdout.readline()
            output += currentOutput
        return output.replace('\n#FIM\n', '')

    def readStderr(self):
        output = ''
        currentOutput = ''
        while(currentOutput != '#FIM\n'):
            currentOutput = self.__stderr.readline()
            output += currentOutput
        return output.replace('\n#FIM\n', '')
