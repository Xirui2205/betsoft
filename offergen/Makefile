PROG=offergen.py
PY=offergen/*.py offergen/format/*.py offergen/report/*.py contrib/*.py
PYC=offergen/*.pyc offergen/format/*.pyc offergen/report/*.pyc contrib/*.pyc
DATA=cls/* template/* strings/* static/* static/js/* doc/* workdir output logs VERSION CHANGELOG INSTALL Makefile
VER=$(shell (cat VERSION ; echo "print '%d.%d' % (versionmajor, versionminor)") | python -)

compile:
	py_compilefiles $(PY)

tarball: compile
	tar --no-recursion -czf offergen-$(VER).tar.gz $(PROG) $(PY) $(PYC) $(DATA)

clean:
	rm -f $(PYC)

