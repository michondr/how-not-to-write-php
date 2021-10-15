# nas ukol

Zopakujte si úpravy, které jsme dělali na cvičení (viz prezentace s postupem). Následně se pokuste za domácí úkol aplikaci upravit:

1) upravte výpis úkolů tak, aby byly vypsány v přehlednější podobě (můžete využít např. tabulku či jinou vhodnou HTML strukturu) a u každého úkolu byl vidět:
    * stav (vizuálně odlište hotové úkoly např. jejich přeškrtnutím
    * jednotlivé tagy
    * deadline, pokud je u úkolu zadaný
2) stránka s detailem úkolu
   * na stránce bude vidět název úkolu, jeho tagy,
   ke každému úkolu doplňte možnost označit jej jako splněný (či případně jako nesplněný)
   u úkolu bude odkaz, na který když uživatel klikne, změní se stav úkolu

3) Odevzdejte textový soubor (txt) s URL, pod kterou je funkční aplikace k dispozici na serveru.
Pokud by se vám některá část nepodařila, je možné odevzdat i jen částečné řešení...

[výsledek (implementace) běží zde](+)

# co jsem dotáhl navíc
* editaci todo včetně multiselectu na tagy
* vytvoření nového tagu
* rozdělení todos na nesplěné a splněné
* do seznamu tagů přidal i description
* do seznamu přidal možnost na okamžité odškrtnutí nebo vrácení do původního stavu (klik na title), protože jsem to udělal dřív než jsem si přečetl v zadání že to má být v detailu (show routa)

# na co jsem se totálně vykašlal
* kvalita, čitelnost, energie dotáhnout to do nějaké kvality, hledání error stavů, validace 

# moje poznámky

moje výkřiky do tmy když jsem se to dělal:

musím říct, že dokumentace veliký trash. berte to prosím jako můj osobní názor - pokud dibi ještě není mrtvá, tak brzy umře.

1 krok - řekl jsem si že seřadím todočka z DB podle toho kdy mají deadline desc, secondary podle toho v jakém pořadí vyly vytvořeny - v sql jednoduše jako order by deadline desc, created desc. (pak jsem zjistil že nemáme slupec "created_at)
* hledám jak zadat order. ta metoda je totálně magická 
 
[![](https://i.imgur.com/F3Y9L9l.png )](https://i.imgur.com/F3Y9L9l.png)

* tak jdu hledat dokumentaci v kódu. no doprčic, tohle používá dibi 
 
[![](https://i.imgur.com/BADgaLZ.png )](https://i.imgur.com/BADgaLZ.png)

* zavzpomínám na první práci, zamáčknu slzu a jdu googlit
* (v téhle pozici jsem naštvanej že jsem jen z kódu nemohl zjistit jak se to používá)
* dostanu se na https://apidoc.intm.org/tharos/leanmapper/v3.4.1/namespace-LeanMapper.html, hledám podle ctrl+f, hledám v search baru, nejde potvrdit nikam mě to neredirectne. neřekne mi že nemá vyýsledky, jen tupě nic nedělá. umažu pár znaků, něco se začne ukazovat- aha načítá to když píšeš, ale to co hledám tam není
* tak nic, jdu zpět, třeba dibi fluent bude něco vědět - nemůžu se proklikat na description/definici funkce jak to funguje a jak to zapsat aby to udělalo to sql co chci
* pro info jak by to fungovalo když bych to řešil v doctirne 
 
[![](https://i.imgur.com/A3a5v1X.png )](https://i.imgur.com/A3a5v1X.png)

* hunt continues - jdu googlit v češtině, protože lidi které chceme učit programovat na téhle škole uričtě nikdy nebudou pracovat anglicky... 🙄
    * jsme tak rozvinutá businesss škola, učíme se jazyky, ale informace se tady učíme pořád hleda v češtině - v čr je 10 milionů lidí, anglicky mluví 1.35 miliardy lidí na světě. hledat informace v češtině je nesmysl
* dostanu se an nějaký random thread na foru co jsem nikdy neviděl, s jednou otázkou a jednou odpovědí 
 
[![](https://i.imgur.com/5LN8bO1.png )](https://i.imgur.com/5LN8bO1.png)

* kde jsou tak hodní že mi ukážou jak by to ohlo fungovat, a odkážou mě do kódů kde to můžu najít, a voila, jsme zpět na Fluent.php


výsledek:
* nezjistil jsem PROČ se to tak děje
* nezjistil jsem jak to funguje, stejně si budu muset chvíli hrát s nějakým blackboxem abych odhadl JAK to to teda funguje a používá se
* vyhodil jsem 20 minut svého života. takhle se pozná špatný kód - krade z lidí životní energii, čas, peníze a štěstí.
* dobrý kód je self explanatory, nebo se dá najít řešení
 


2) krok, přidávám deadline abych mohl vizualně vidět že to maká
* přidám do kódu (předpokládám že latte ma date filtery, padne to 
 
[![](https://i.imgur.com/iHEJ9ta.png )](https://i.imgur.com/iHEJ9ta.png)

    * to je přesně ten důvod, proč jsem se na hodině ptal, proč nevracíme \DateTimeImmutable - to je univerzál
* tak jinudy - co to toto, proč to nevidí? 
 
[![](https://i.imgur.com/3RlKci1.png )](https://i.imgur.com/3RlKci1.png)

* aha, špatný import.... mg, nechápu jak je to related k té chybě, ale dobře
 
[![](https://i.imgur.com/h16kzLc.png )](https://i.imgur.com/h16kzLc.png)


* přidám si tabulku, chci odděleně pole takových a makových
   * jsme zase na začátku u toho jak se používají ty find funkce fakt nelogické
 
[![](https://i.imgur.com/9pofJ8R.png )](https://i.imgur.com/9pofJ8R.png)

* dal jsem se do formuláře, a nad tímhle mi stála hlava. proč, prosím proč. jestli mi to někdo vysvětlíte, budu nesmírně rád

[![](https://i.imgur.com/5CkJA9a.png )](https://i.imgur.com/5CkJA9a.png)
