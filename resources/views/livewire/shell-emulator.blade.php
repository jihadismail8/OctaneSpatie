<div>
    {{-- Success is as dangerous as failure. --}}
    <div id="shell-1"></div>
    @push('scripts')
    <script type="module">
        import { JsShell } from '../jsShell.js';

        const commands = {
            color: {
            handler: changeColor,
            description: 'Random background & text color'
            },
            clear: {
            handler: (shell) => {
                shell.clear();
            },
            description: 'Clear console.'
            },
            cd: {
            handler: (shell, argv) => {
                shell.setPrompt(makePS1(argv[1] || '~'));
            },
            description: 'cd [dir] Change working directory.'
            },
            form: {
            handler: form,
            description: 'Run a cli form, just to demonstrate.'
            },
            github: {
            handler: () => {
                window.location.href = 'https://github.com/francoisburdy/js-shell-emulator';
            },
            description: 'Redirect to package Github repo'
            },
            help: {
            handler: helpMenu,
            description: 'Show available commands'
            },
            joke: {
            handler: joke,
            description: 'Tell a joke'
            },
            reboot: {
            handler: startShell,
            description: 'restart shell process'
            },
            time: {
            handler: (shell) => {
                shell.print((new Date()).toString());
            },
            description: 'What time is it?'
            },
            exit: {
            handler: (shell) => {
                shell.print('Bye!');
            },
            description: 'Stop the shell prompt'
            }

        };

        const bgColors = [
            '#000',
            'rgb(33,42,56)',
            'rgb(85,16,16)',
            'rgb(82,34,7)',
            'rgb(9,64,61)',
            'rgb(9,66,97)',
            'rgb(63 63 70)',
            'rgb(58,8,99)',
            'rgb(57,27,2)'
        ];

        const textColors = [
            '#FFF',
            '#0F0',
            '#fffd2f',
            '#ff1c1c'
        ];

        function padToNChars(string, size) {
            let newString = '';
            for (let index = 0; index < (size - string.length); index++) {
            newString = newString + ' ';
            }
            return string + newString;
        }

        function helpMenu(shell) {
            shell.printHTML('GNU Fake Shell, version 0.1.12-not-released\n' +
            'These shell commands are defined internally.  Type \'help\' to see this list.\n' +
            'Use Google to find out more about the shell in general and to find out more about commands not in this list.');

            shell.print('--------------------------------------------------------------');
            shell.print('| Command       |  Description                               |');
            shell.print('|---------------|--------------------------------------------|');
            for (const key in commands) {
            shell.print(`| ${padToNChars(key, 14)}| ${padToNChars(commands[key].description, 43)}|`);
            }
            shell.print('--------------------------------------------------------------');
        }

        async function joke(shell) {
            const response = await fetch('https://icanhazdadjoke.com/', {
            headers: { Accept: 'application/json' }
            });
            const body = await response.json();
            if (response.ok) {
            await shell.type(body.joke, 20);
            }
            shell.newLine();
        }

        async function form(shell) {
            const originalPS1 = shell._promptPS1.innerHTML;
            shell.clear()
            .setPrompt('> ')
            .printHTML('<strong class="text-green-400">Please fulfill the following form. Believe it or not, data won\'t leave the browser.</strong>');
            const name = await shell.input('What\'s your name ?');
            shell.print(`Hello ${name}`);

            await shell.newLine().input('Enter your email address');
            await shell.newLine().password('Enter your password');

            shell.newLine().print('Thanks... Let me check that...just a second');
            await JsShell.sleep(1500);
            const really = await shell.newLine().confirm('You didn\'t enter your real password, did you?');
            if (really) {
            shell.print('Oh gosh... Be sure I accept no responsibility.').newLine();
            } else {
            shell.print('I like this better.');
            }

            shell.print('We\'re done. Thank you!').newLine();
            await shell.pause('PRESS ANY KEY TO CONTINUE');
            shell.setPrompt(originalPS1);
        }

        function changeColor(shell) {
            let newBgColor, newTextColor;
            do {
            newBgColor = bgColors[Math.floor(Math.random() * bgColors.length)];
            } while (newBgColor === shell.html.style.background);

            do {
            newTextColor = textColors[Math.floor(Math.random() * textColors.length)];
            } while (newTextColor === shell.html.style.color);

            shell
            .setBackgroundColor(newBgColor)
            .setTextColor(newTextColor);
        }

        function makePS1(path) {
            if (path.slice(-1) !== '/') {
            path += '/';
            }
            return `<strong class="text-green-400">\nuser@machine:${path}\n$ </strong>`;
        }

        async function startShell(shell) {
            shell
            .clear()
            .print('Hello, world!')
            .printHTML('This is a more sophisticated <a href=\'https://github.com/francoisburdy/js-shell-emulator\'>francoisburdy/js-shell-emulator</a> demo.')
            .printHTML('See source code <a href="https://github.com/francoisburdy/js-shell-emulator/blob/master/demos/complex.html">here</a>.')
            .print('Type "help" to see the commands list.');

            let input = '';
            while (input !== 'exit') {
            input = await shell.input();
            input = input.trim();
            if (!input.length) {
                continue;
            }
            const argv = input.split(' ');
            if (Object.hasOwn(commands, argv[0])) {
                await commands[argv[0]].handler(shell, argv);
            } else {
                shell.print(`${argv[0]}: command not found`);
            }
            }
        }

        const shell1 = new JsShell('shell-1', {
            width: '100%',
            height: '80vh',
            textSize: '0.9rem',
            backgroundColor: bgColors[0],
            promptPS: makePS1('~/fake/path')
        });

        startShell(shell1);

    </script>
    @endpush
</div>
