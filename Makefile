SRCDIR ?= /opt/fpp/src
include $(SRCDIR)/makefiles/common/setup.mk
include $(SRCDIR)/makefiles/platform/*.mk

all: libshowpilot.$(SHLIB_EXT)
debug: all

OBJECTS_fpp_showpilot_so += src/FPPShowPilotSync.o
LIBS_fpp_showpilot_so += -L$(SRCDIR) -lfpp
CXXFLAGS_src/FPPShowPilotSync.o += -I$(SRCDIR)

%.o: %.cpp Makefile
	$(CCACHE) $(CC) $(CFLAGS) $(CXXFLAGS) $(CXXFLAGS_$@) -c $< -o $@

# FPP constructs the shlib path as: lib + <plugin-dir-name> + .so
# Our plugin directory is named 'showpilot', so FPP looks for libshowpilot.so.
libshowpilot.$(SHLIB_EXT): $(OBJECTS_fpp_showpilot_so) $(SRCDIR)/libfpp.$(SHLIB_EXT)
	$(CCACHE) $(CC) -shared $(CFLAGS_$@) $(OBJECTS_fpp_showpilot_so) $(LIBS_fpp_showpilot_so) $(LDFLAGS) -o $@

clean:
	rm -f libfpp-showpilot.$(SHLIB_EXT) libshowpilot.$(SHLIB_EXT) $(OBJECTS_fpp_showpilot_so)
