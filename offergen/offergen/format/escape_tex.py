import re


rx = re.compile(r'([\\{}$&#^_%~])')


def repl_func(m):
    c = m.group(1)
    if c == '\\': return r'$\backslash$'
    if c in ('^','~'): return r'\%c{}' % c
    return '\\' + c


def escape_tex(text):
    global rx
    return rx.sub(repl_func, text)


def escape_tex_dict(d):
    for key in d:
        if d[key] is None:
            d[key] = ''
            continue
        if type(d[key]) == str:
            d[key] = escape_tex(d[key])


# self test
if __name__ == '__main__':
    d = {1:r'\bla{}', 2:r'\{}$&#^_%~', 3:r'bla \ bla $ bla # bla', 4:None}
    escape_tex_dict(d)
    for k in d:
        print d[k]

