\NeedsTeXFormat{LaTeX2e}
\ProvidesClass{nabidka}

%% Parametry tridy
\newif\iftwocolumnoption \twocolumnoptionfalse
\newif\iflandscapeoption \landscapeoptionfalse
\DeclareOption{twocolumn}{\twocolumnoptiontrue}
\DeclareOption{landscape}{\landscapeoptiontrue}
\DeclareOption*{\PassOptionsToPackage{\CurrentOption}{geometry}}
\ProcessOptions

%% Velikost papiru
\iflandscapeoption
  \setlength{\paperwidth}{297mm}
  \setlength{\paperheight}{210mm}
\else
  \setlength{\paperwidth}{210mm}
  \setlength{\paperheight}{297mm}
\fi

%% Pouzite balicky
\RequirePackage[utf8]{inputenc}
\RequirePackage[T1]{fontenc}
%\usepackage[condensed,math]{iwona}
\usepackage[scaled]{helvet}
\RequirePackage{color}
\RequirePackage{graphicx}
\RequirePackage[includehead,nofoot,head=12pt,headsep=5pt]{geometry}
\RequirePackage{fancyhdr}

% Cisla stranek
\def\thepage{\the\count0}
\RequirePackage{lastpage}

%% Pisma
\renewcommand{\seriesdefault}{mc}
\renewcommand*\familydefault{\sfdefault}
\renewcommand{\normalsize}{\fontsize{1.4\msize}{1.6\msize}\selectfont}
\newcommand{\tabsize}{\fontsize{1.4\msize}{1.6\msize}\selectfont}
\newcommand{\datesize}{\fontsize{1.4\msize}{1.6\msize}\selectfont}
\newcommand{\sporttitlesize}{\fontsize{2\msize}{2.2\msize}\selectfont}
\newcommand{\eventtitlesize}{\fontsize{1.6\msize}{1.8\msize}\selectfont}
\newcommand{\titlesize}{\fontsize{1.4\msize}{1.6\msize}\selectfont}
\newcommand{\pageheadsize}{\fontsize{1.6\msize}{1.8\msize}\selectfont}

%% Vysky boxu
\newcommand{\sporttitlestrut}{\vrule height 1.6\msize depth 2pt width 0pt}
\newcommand{\eventtitlestrut}{\vrule height 1.2\msize depth 2pt width 0pt}

%% Barvy
% barva pozadi ramecku data, nadpisu a hlavicek
\definecolor{bgtabtitlesport}{gray}{1}
\definecolor{bgtabtitleevent}{gray}{1}
\definecolor{bgtabtitle}{gray}{1}
\definecolor{bgtabsubtitle}{gray}{1}
\definecolor{bgtabhead}{gray}{1}

\columnsep=1em
\iflandscapeoption
  \newdimen\msize \msize=8pt
\else
  \newdimen\msize \msize=6pt
\fi

%% Mezera mezi nadpisy
\newdimen\intskipamount
\intskipamount=8pt
\def\intskip{\vskip\intskipamount}

\newdimen\titleskip \titleskip=3pt
\newdimen\datumskip \datumskip=12pt

\def\hskiptitle{\hskip \titleskip}

%% Paticka
\pagestyle{fancy}
\fancyhf{}
\renewcommand{\headrulewidth}{0pt}
\renewcommand{\footrulewidth}{0pt}

\fancyhead[L]{\pageheadsize\bfseries CompBet}
\fancyhead[C]{\pageheadsize\bfseries\MakeUppercase{\@title}}
\fancyhead[R]{\pageheadsize Strana \thepage~/~\pageref{LastPage}}

\parskip=0pt
\splittopskip=0pt
\topskip=3pt


\AtBeginDocument{%
  \DeclareUnicodeCharacter{00B4}{'}  %% acute accent
  %% dva sloupce
  \iftwocolumnoption
    \twocolumn
  \fi
}


%% Nastavitelne promenne
\def\@title{}
\def\settitle#1{\def\@title{#1}}


\def\celltitle#1{\colorbox{bgtabtitle}{\bfseries#1\mdseries}}

%% Barevny ramecek pres sirku stranky
\newcommand{\widefcolorbox}[4]{\vbox{%
\setlength\fboxrule{#1}
\setlength\fboxsep{0pt}
\dimen0=\hsize
\advance\dimen0 by -2\fboxrule
\noindent\fcolorbox{#2}{#3}{\hbox to \dimen0{#4}}\vfil}\intskip}

\newbox\boxtabtitlesport
\def\tabtitlesport#1{%
  \newif\ifforcetabhead \forcetabheadfalse
  \setlength\fboxsep{0pt}
  \setbox\boxtabtitlesport=\vbox{%
    \hrule
    \noindent\colorbox{bgtabtitlesport}{\hbox to \hsize{\hfil\sporttitlesize\bfseries\sporttitlestrut#1\hfil}}
    \hrule
    \vskip1pt}
  \forcetabheadtrue
  \unvbox\boxtabtitlesport}

\newbox\boxtabtitleevent
\def\tabtitleevent#1{%
  \newif\ifforcetabhead \forcetabheadfalse
  \setlength\fboxsep{0pt}
  \setbox\boxtabtitleevent=\vbox{%
    \noindent\colorbox{bgtabtitleevent}{\hbox to \hsize{\hfil\eventtitlesize\bfseries\eventtitlestrut#1\hfil}}
    \hrule
    \vskip1pt}
  \forcetabheadtrue
  \unvbox\boxtabtitleevent}

\newbox\boxtabtitle
\def\tabtitle#1{%
  \newif\ifforcetabhead \forcetabheadfalse
  \setlength\fboxsep{0pt}
  \setbox\boxtabtitle=\vbox{%
    \noindent\colorbox{bgtabtitle}{\hbox to \hsize{\titlesize\bfseries\strut\hskiptitle#1\hfil}}
    \vskip1pt}
  \forcetabheadtrue}

\def\tabsubtitle#1{%
  \newif\ifforcetabhead \forcetabheadfalse
  \setlength\fboxsep{0pt}
  \setbox\boxtabtitle=\vbox{%
    \noindent\colorbox{bgtabsubtitle}{\hbox to \hsize{\strut#1\hfil}}
    \vskip1pt}
  \forcetabheadtrue}

\def\tabtitleandsubtitle#1#2{%
  \newif\ifforcetabhead \forcetabheadfalse
  \setlength\fboxsep{0pt}
  \setbox\boxtabtitle=\vbox{%
    \noindent\colorbox{bgtabsubtitle}{\hbox to \hsize{\strut#2\hfil}}    
    \vskip1pt}\vbox{%
    \noindent\colorbox{bgtabtitle}{\hbox to \hsize{\titlesize\bfseries\strut\hskiptitle#1\hfil}}
    \vskip1pt}
  \forcetabheadtrue}

%% Hlavni tabulka - vetsi pismo
%% nastavi if a vola normalni tabulku
\newif\ifhlavni \hlavnifalse

%% Tabulka, parametrem je pocet sloupcu s vysledky
\newdimen\diml
\newdimen\dimm
\newdimen\dimc
\newdimen\maxdimc
\newdimen\dimd
\newdimen\dimx
\newdimen\dimy
\newdimen\dimz
\newdimen\dimr
\newdimen\dimcm
\newcount\tmpnum
\newcount\ncol
\newcount\ncoltmp
\newbox\boxtabhead

\def\tab#1{%
  \begingroup
  \tabsize
  \ncol=#1
  \dimen0=1.4\msize % vyska radku
  \dimen1=1.4\msize % zakladni sirka sloupcu
  \setlength\fboxsep{0pt}
  \baselineskip=1.1\dimen0
  \def\podpera{\vrule height 0.9\dimen0 depth 0.2\dimen0 width 0pt}
  % nazev
  \dimm=\hsize
  % info
  \diml=5.1\msize
  \advance\dimm by -\diml
  % kursy
  \ifnum\ncol>4
    \dimc=2.1\dimen1
    \maxdimc=6\dimc  \divide\maxdimc by\ncol
  \else
    \dimc=2.8\dimen1
    \maxdimc=4\dimc  \divide\maxdimc by\ncol
  \fi
  \dimcm=\dimc
  \multiply\dimcm by \ncol
  \advance\dimm by -\dimcm
  % ako, datum
  \dimr=2.5\dimen1
  \dimx=2.35\dimen1
  \dimy=2.7\dimen1
  \dimz=0.4\dimen1
  \dimd=4.5\dimen1
  \advance\dimm by -\dimr
  \advance\dimm by -\dimd
  \def\tightenlines{\lineskiplimit-10pt\lineskip=0pt\baselineskip=1.0\dimen0}
  % l(info) m(nazev) c c c c [d] [r]
  \def\boxl##1{\parbox[t]{\diml}{\raggedright##1}}
  \def\boxm##1{\parbox[t]{\dimm}{\tightenlines\raggedright##1}}
  \def\boxc##1{\parbox[t]{\dimc}{\tightenlines\centering\hskip0pt##1}}
  \def\boxca##1{\parbox[t]{\dimy}{\tightenlines\centering\hskip0pt##1}}
  \def\boxcb##1{\parbox[t]{\dimx}{\tightenlines\centering\hskip0pt##1}}
  \def\boxcc##1{\parbox[t]{\dimz}{\tightenlines\centering\hskip0pt##1}}
  \def\boxr##1{\parbox[t]{\dimr}{\centering\rcolumnattr{##1}}}
  \def\boxd##1{\parbox[t]{\dimd}{\rcolumnattr{\datesize\raggedright##1}}}
  %
  %% Definice radku tabulky pro ruznou sirku
  \def\tabline##1##2{\hbox to \hsize\bgroup%
    \podpera\boxl{##1}\boxm{##2}\hfil\let\next=\dalsiparam\next}
  \def\dalsiparam##1{\boxc{##1}\advance\ncol by-1 %
    \ifnum\ncol=0 %
      \let\next=\akoparam%
    \fi\next}
  \def\akoparam##1{\datumparam}
  \def\datumparam##1{\boxd{##1}\tablinekonec}
  \def\tablinekonec{\egroup\par\penalty0}
  %
  %% Definice radku tabulky pro ruznou sirku
  \def\tablineb##1##2{\hbox to \hsize\bgroup%
    \podpera\celltitle{\boxca{##1}}\boxcb{##2}\boxcc{}\let\next=\dalsiparamb\next}
  \def\dalsiparamb##1##2{\celltitle{\boxca{##1}}\boxcb{##2}\boxcc{}\advance\ncol by-2 %
    \ifnum\ncol=0 %
      \let\next=\tablinekonecb%
    \fi\next}
  \def\akoparamb##1{\datumparamb}
  \def\datumparamb##1{\boxc{##1}\tablinekonecb}
  \def\tablinekonecb{\egroup\par\penalty0}
  %
  \def\rcolumnattr{}
  %
  \def\headline{%
    \head
    \def\cols{}
    \ncoltmp=\ncol
    \advance \ncoltmp by2
    \tmpnum=2
    \let\next=\headlinedalsi\next}
  \def\headlinedalsi##1{\edef\cols{\cols{##1}} %
    \advance\ncoltmp by-1 %
    \ifnum\tmpnum>0 %
      \advance\tmpnum by-1 %
    \else %
      \setbox7=\hbox{\bfseries~##1~} %
      \ifdim \wd7>\dimc %
        \global\dimc=\wd7 %
        \ifdim \dimc>\maxdimc %
          \global\dimc=\maxdimc
        \fi %
      \fi%
    \fi%
    \ifnum\ncoltmp=0
      \let\next=\headlineako
    \fi\next}
  \def\headlineako##1{\edef\cols{\cols{##1}}\headlinedatum}
  \def\headlinedatum##1{\edef\cols{\cols{##1}}\headlinekonec}
  \def\headlinekonec{%
    %% upravit sirku sloupcu podle zjistene sirky textu v hlavicce
    \global\advance\dimm by \dimcm %
    \global\dimcm=\dimc %
    \global\multiply\dimcm by \ncol %
    \global\advance\dimm by -\dimcm %
    \expandafter\tabline\cols\endhead}
  %
  \def\head{%
    %\par\bigskip\penalty0
    \setbox\boxtabhead=\vbox\bgroup\penalty0
    \def\rcolumnattr{}
    \bfseries
    }
  %
  \def\endhead{%
    \vfil\egroup
    \def\tabheadpodpera{\vrule height \ht\boxtabhead width 0pt}
    \setbox\boxtabhead=\vbox{%
    \hbox{\colorbox{bgtabhead}{\hbox to \hsize{\tabheadpodpera\hfil}}}
    \vskip-\ht\boxtabhead
    \unvbox\boxtabhead
    \vfil \vskip-1pt
    }
    \par\penalty0
    \setbox0=\vbox\bgroup\penalty0}
  %
  
  \def\headlineB{%
    
    \expandafter\tablineb\endheadB}
  %
  \def\endheadB{%
    
    \setbox0=\vbox\bgroup\penalty0
    }
  %
  
  
  \def\endtab{%
    %\vfill
    \egroup
    \endtab@
    \endgroup
    \par\penalty0\vskip3pt
  }
}


% Interni makro pro uzavreni tabulky
% pouzije boxy s hlavickami a box s tabulkou
% box s tabulkou rozlomi do stranek a pripadne rozdeli na dva sloupce
\newif\ifheadonfirst
\def\endtab@{%
  %\par\nobreak
  \setbox1=\vsplit0 to0pt
  %% zbyle misto do \dimen1
  \ifdim\pagegoal=\maxdimen
    %% jsme na nove strance
    \dimen1=\vsize
    \headonfirsttrue
  \else
    \dimen1=\pagegoal \advance\dimen1 by-\pagetotal
    \headonfirstfalse
  \fi
  \ifforcetabhead
    \global\forcetabheadfalse
    \headonfirsttrue
  \fi
  %% pokud se na stranku nevejde jeden radek tabulky + hlavicka,
  %% prejit na dalsi stranku
  \dimen3=\ht\boxtabhead
  \advance\dimen3 by\dp\boxtabhead
  \advance\dimen3 by 15pt %head
  \advance\dimen3 by\topskip
  \advance\dimen3 by\splittopskip
  %% rezervovane misto pro prvni stranu
  \dimen4=\dimen3
  \ifheadonfirst
    \advance\dimen4 by\ht\boxtabtitle
    \advance\dimen4 by\dp\boxtabtitle
  \fi
  %% upravit velikost rezervovaneho mista pro dalsi strany
  \advance\dimen3 by\ht\boxtabtitle
  \advance\dimen3 by\dp\boxtabtitle
  %% zmensit zbyle misto o rezervovany prostor
  \advance\dimen1 by-\dimen4
  %% pokud se nevejdou na prvni stranu dva radky tabulky, prejit hned na dalsi
  \dimen5=2\baselineskip
  \ifdim\dimen1<\dimen5
    %% na dalsi stranu jen, pokud se nevejde cela dabulka (muze mit 2 a mene radku)
    \dimen0=\ht0 \advance\dimen0 by\dp0
    \ifdim\dimen0>\dimen1
      \vfil\break \dimen1=\vsize
      \advance\dimen1 by-\dimen4
    \fi
  \fi
  %% lamat tabulku
  \splittabfirst}

\def\splittabfirst{%
  \ifheadonfirst
    \nointerlineskip\hbox to \hsize{\copy\boxtabtitlesport \copy\boxtabtitleevent \copy\boxtabtitle \hss}
  \fi
  \splittab@
  \ifvoid0\else
    \dimen1=\vsize
    \advance\dimen1 by-\dimen3
    \splittab
  \fi}

\def\splittab{%
  \nointerlineskip\hbox to \hsize{\hss \copy\boxtabtitlesport \copy\boxtabtitleevent \copy\boxtabtitle \hss}
  \splittab@
  \ifvoid0\else
    \dimen1=\vsize
    \advance\dimen1 by-\dimen3
    \splittab
  \fi}

\def\splittab@{%
  \nointerlineskip\hbox to \hsize{\copy\boxtabhead\hss}
  \dimen0=\ht0 \advance\dimen0 by\dp0
  \ifdim\dimen1>\dimen0
    % tabulka se vejde pohodlne cela
    %\nointerlineskip\hbox to \hsize{\vbox to\dimen0{\unvbox0\vfil}}
    \nointerlineskip\hbox to \hsize{\box0}
    \par\nobreak
  \else
    \setbox5=\vsplit0 to\dimen1
    \setbox5=\vbox{\unvbox5}
    \orezbox
    \nointerlineskip\hbox to \hsize{\box5}
    \vfil\break
  \fi}

%% zkrati box5 na max. velikost dimen1
\def\orezbox{\dimen9=\dimen1\orezbox@}
\def\orezbox@{%
  % dimen1 - pozadovana vyska
  % dimen9 - aktualni vyska lomu
  % dimen8 - vyska boxu 0 (zbyvajici material)
  \let\next=\relax
  \dimen8=\ht5 \advance\dimen8 by\dp5
  \ifdim\dimen8>\dimen1
    % vratit posledni radek z box5 do box0
    \advance\dimen9 by -\baselineskip
    \setbox9=\vsplit5 to\dimen9
    \setbox0=\vbox{\unvbox5\unvbox0}
    \setbox5=\vbox{\unvbox9}
    \if\dimen9>\baselineskip
      \let\next\orezbox@
    \fi
  \fi
  \next}

