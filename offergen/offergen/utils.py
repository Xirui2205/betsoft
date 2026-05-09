import unicodedata
import time, calendar

def uncarkyhacky(s):
    if not isinstance(s, unicode):
        s = unicode(s, 'utf-8')
    s = unicodedata.normalize('NFKD', s)
    output = ''
    for c in s:
        if not unicodedata.combining(c):
            output += c
    return output.decode('utf-8')


def reformatDate(dt, destformat='%x', srcformat='%Y-%m-%d %H:%M'):
    if dt is None:
        return None
    return time.strftime(destformat, utc_to_local(time.strptime(dt, srcformat)))

def utc_to_local(t):
    secs = calendar.timegm(t)
    return time.localtime(secs)


