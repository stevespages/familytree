# familytree v0.0.0

This project provides software for creating and maintaining a family tree. It is based on the [The FamilySearch GEDCOM Specification](https://gedcom.io/specifications/FamilySearchGEDCOMv7.html#cb26-1) 7. The data is stored in SQL tables which can be generated from a GEDCOM 7 file and can generate a GEDCOM 7 file.

It will take time to fully implement the GEDCOM specification. During that time gedcom files may be read by this software and only some of the data represented in SQL tables. In order not to lose any data the original gedcom file will be kept by this application in a write protected manner and be associated with the SQL representation so further development can incorporate previously ignored data.

## SQL

The correspondence between the GEDCOM format and the SQL database representing is not simple. Although level zero tags such as HEAD, INDI AND FAM have corresponding tables (head, indis and fams), the columns do not correspond precisely. Other tables, especially where there are one to many or many to many relationships, are used. For example, SCHMA is a tag in the HEAD section. It should be represented with another table, schmas, with columns, tag and uri. It does not need a foreign key to the head table because there is only one row in the head table because the GEDCOM format specifies there can only be one HEAD section.

Cross-reference identifiers (xref) are not retained between data streams and should not be displayed. This means that they can be generated from the id INTEGER AUTOINCREMENT PRIMARY KEY column of a table and do not need to have a column of their own. They could be generated from the SQL tables' id columns when a GEDCOM file is generated. Using the id columns like this should result in faster database queries than using xref values as foreign keys.

GEDCOM7 specifies that "If a FAM record uses CHIL to point to an INDI record, the INDI record must use a FAMC to point to the FAM record". I have not seen a requirement for the reciprocal. Also, an INDI record can contain substructures for its FAMC tags such as PEDI (born into family or adopted etc.). As it appears to be redundant to record the same children in both the INDI and the FAM records, this application will only store information about a child's relation to a family(s) in the indis table. When the application creates a GEDCOM7 file it will create the required records to comply with the specification. Because in a significant number of cases a child will have two or more families (for example born into family from first marriage and then part of one or both parent's second marriage families), a linking table called indis_fams, will contain the id of the individual and the ids of their families. Also the table will contain colums related to the PEDI tag.

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

- resn

  TEXT ENUM(PRIVACY | NONE | NULL)

  RESN Structure Type. Restriction. g7 ENUM specification is different but includes PRIVACY.

- givn

  TEXT

  GIVN Structure Type. Given Name.

- surn

  TEXT

  SURN Structure Type. Surname.

- middle

  TEXT

  Can be used with givn and surn to create: *INDI-NAME -> PERSONAL_NAME_STRUCTURE -> PersonalName*. See PERSONAL_NAME_PIECES for example.

- npfx

  TEXT

  eg. "Mr" or "Lord". As a person can have more than one npfx, this could be implemented as a linking table eg. make npfxs and indis_npfxs_xref

- sex

  TEXT ENUM(M | F | X | U)

  SEX Structure Type. Sex. X: Does not fit the typical definition of only Male or only Female. U: Cannot be determined from available sources

### fams

- id

  INTEGER AUTOINCREMENT

- husb

  TEXT FK (indis.id)
  
- wife

  TEXT FK (indis.id)

### indis_fams_xref

- id

  INTEGER AUTOINCREMENT

- id_indis

  INTEGER FK

- id_fams

  INTEGER FK

- pedi

  TEXT ENUM(ADOPTED | BIRTH | FOSTER | SEALING | OTHER)

- phrase

  TEXT

  I think GEDCOM7 only allows a PHRASE tag if the value of pedi is OTHER. Not sure??



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

More columns need to be added to enable a full implementation of GEDCOM. For example IDENTIFIER_STRUCTURE, NOTE_STRUCTURE, SOURCE_CITATION, CHANGE_DATE and CREATION_DATE could be catered for.

- id

  INTEGER PRIMARY KEY

- resn

  TEXT ENUM()

- form

  TEXT

  Media type eg "jpeg"

  Medium

- titl

  TEXT

  Title

- file

  TEXT

  Eg. path to a photo file. Can be a local path or a URL. Can be a relative URL eg "../media/photos/fred.jpg"

### sours

The sour table may point to other tables for efficient recording of references etc....

- id

  INTEGER PRIMARY KEY

- 

## SQL Queries

SELECT i.givn, i.surn FROM individuals i LEFT JOIN families f ON i.famc = f.xref WHERE  f.xref = :xref;