from lxml import etree

class Strings:
    def __init__(self, e_sel, e_common):
        self.e_sel = e_sel
        self.e_common = e_common


    def get(self, key):
        if not self.e_sel is None:
            res = self.e_sel.find(key)
            if res is not None:
                return res.text
        res = self.e_common.find(key)
        if res is not None:
            return res.text
        return None


    def __getitem__(self, key):
        res = self.get(key)
        if res is None:
            return None
        return res.encode('utf-8')


    def __getattr__(self, name):
        res = self.get(name)
        if res is None:
            raise AttributeError(name)
        return res.encode('utf-8')


class StringsLoader:
    def __init__(self, fname):
        self.e_root = etree.parse(fname).getroot()
        self.e_common = self.e_root.find('common')


    def getStrings(self, section):
        e_sel = self.e_root.find(section)
        return Strings(e_sel, self.e_common)



