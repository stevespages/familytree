# familytree v0.0.0

This project provides software for creating and maintaining a family tree. It is based on the [The FamilySearch GEDCOM Specification](https://gedcom.io/specifications/FamilySearchGEDCOMv7.html#cb26-1) 7. The data is stored in SQL tables which can be generated from a GEDCOM 7 file and can generate a GEDCOM 7 file.

It will take time to fully implement the GEDCOM specification. During that time gedcom files may be read by this software and only some of the data represented in SQL tables. In order not to lose any data the original gedcom file should be kept in a write protected manner and be associated with the SQL representation so further development can incorporate previously ignored data.

## SQL

The correspondence between the GEDCOM format and the SQL database representing it is not simple. Although level zero tags such as HEAD, INDI AND FAM have corresponding tables, head, indis, fams, the columns do not correspond precisely and other tables, especially where there are one to many or many to many relationships, are used. For example, SCHMA is a tag in the HEAD section. It should be represented with another table, schmas, with columns, tag and uri. It does not need a foreign key to the head table because there is only one row in the head table because the GEDCOM format specifies there can only be one HEAD section.

Cross-reference identifiers (xref) are not retained between data streams and should not be displayed. This means that they can be generated from the id INTEGER AUTOINCREMENT PRIMARY KEY column of a table and do not need to have a column of their own. They could be generated from the SQL tables' id columns when a GEDCOM file is generated. Using the id columns like this should result in faster database queries than using xref values as foreign keys.

### head

- id

  INTEGER AUTOINCREMENT

- gedc_vers

  TEXT

  This is GEDC-VERS eg "7.0.1" or "7.0" but NOT "7".

- sour_vers

  TEXT
  
  SOUR is the software that created this GEDCOM file

- sour_name

  TEXT

- sour_corp

  TEXT
  
  Name of corporation that produced the software. Should be broken down into several fields eg phon, email etc etc.

- sour_data

  TEXT
  
  Name of the source of the data. Should be broken down...

- subm

  TEXT, FK

  should link to xref in subm table




### schmas

- id

  INTEGER AUTOINCREMENT

- tag

  TEXT

  the tag being defined

- uri

  TEXT

  the URI where the schema being associated with the tag is located.

### indis

- id

  INTEGER AUTOINCREMENT

- xref

  TEXT

  This is the cross reference identifier of the INDI *Structure type*. A value of 72 would relate to GEDCOM like this: `0  @I72@ INDI`. Must be text as some GEDCOM ids have leading zeros and it may be convenient (although not necessary) to store the prefix (I in this case) in the database.

- resn

  TEXT ENUM(PRIVACY | NONE | NULL)

  RESN Structure Type. Restriction. g7 ENUM specification is different but includes PRIVACY.

- givn

  TEXT

  GIVN Structure Type. Given Name.

- surn

  TEXT

  SURN Structure Type. Surname.

- middle_names

  TEXT

  Can be used with givn and surn to create: *INDI-NAME -> PERSONAL_NAME_STRUCTURE -> PersonalName*. See PERSONAL_NAME_PIECES for example.

- sex

  TEXT ENUM(M | F | X | U)

  SEX Structure Type. Sex. X: Does not fit the typical definition of only Male or only Female. U: Cannot be determined from available sources

### fams

- id

  INTEGER AUTOINCREMENT

- xref

  TEXT

  This is the cross reference identifier of the FAM *Structure type*. A value of 72 would relate to GEDCOM like this: `0  @F72@ FAM`. Must be text as some GEDCOM ids have leading zeros and it may be convenient (although not necessary) to store the prefix (F in this case) in the database.

- husb

  TEXT FK.individuals.xref

  XREF:INDI
  
- wife

  TEXT FK.individuals.xref

  XREF:INDI

### sours

- 

### notes

It may be best to only implement shared notes (SNOTE).

- id

  INTEGER PRIMARY KEY

- xref

  TEXT

  These xref values need to be unique. They also will be a foreign key in the table(s) the note relates to. It is not obvious how they should be generated. Perhaps by extracting the maximum value from existing rows and incrementing it by 1 to give say, N0044. It is important that the system used can handle valid values from other GEDCOM files.

- note

  TEXT

  The actual note.

### objes

More columns need to be added to enable a full implementation of GEDCOM

- id

  INTEGER PRIMARY KEY

- xref

  TEXT

  Need to be unique. Will be an FK in other table(s).

- resn

  TEXT ENUM()

- form

  TEXT

  Media type

- medi

  TEXT

  Medium

- titl

  TEXT

  Title

### sours

The sour table may point to other tables for efficient recording of references etc....

- id

  INTEGER PRIMARY KEY

- 

## SQL Queries

SELECT i.givn, i.surn FROM individuals i LEFT JOIN families f ON i.famc = f.xref WHERE  f.xref = :xref;