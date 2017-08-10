# mrs-biomarkers

Software Implementation of Fast, Automatic Voxel Positioning for Single Voxel Magnetic Resonance Spectroscopy of Human Brain (AutoVOI Software)

This software allows MR scanners to do spectroscopic scanning of certain structures in the brain.
An anotomical scan is taken, sent to a remote server, and matched against atlases. The voxel coordinates
are calculated for one or more anatomical structures, and returned to the MR scanner for the spectroscopy
scan. The voxel results are calculated and returned quickly enough to be clinically useful for subject scanning.

The software consists of four major parts.

- pipeline: This daemon runs on the BRP server. It takes requests and posts results via the website.
- website: This website exists on the BRP server. Clients post requests here and pool for the results.
- client: This software runs on MR scanners. It sends requests to the website and waits for results.
- sequence: This software runs on MR scanners. It runs a MP-RAGE sequence, then uses the client to find specific voxels.

Take a look in the doc subdirectory for diagrams and more information.
