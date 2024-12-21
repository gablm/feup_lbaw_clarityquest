## ClarityQuest

> ClarityQuest is a Collaborative Q&A platform, aiming to help you answer all your questions about a wide range of topics while giving you the opportunity to provide knowledge to people in need!

## Project Components

* [ER: Requirements Specification](https://gitlab.up.pt/lbaw/lbaw2425/lbaw24125/-/wikis/er)
* [EBD: Database Specification](https://gitlab.up.pt/lbaw/lbaw2425/lbaw24125/-/wikis/ebd)
* [EAP: Architecture Specification and Prototype](https://gitlab.up.pt/lbaw/lbaw2425/lbaw24125/-/wikis/eap)
* [PA: Product and Presentation](https://gitlab.up.pt/lbaw/lbaw2425/lbaw24125/-/wikis/pa)

## Artefacts Checklist

The artefacts checklist is available at: [Checklists [lbaw2405]](https://docs.google.com/spreadsheets/d/1wYn5ffQm20_wKMxD2ZR3perkGi6s6RD3p_9f2RkwdUA/edit?gid=1236141839#gid=1236141839)

## Running the project

The project can be run inside FEUP's network or using VPN by downloading an pre-built image from GitLab's registry. `docker` is required to run the project.

Running the command below will download the latest version of ClarityQuest's image and start it on the background.
```
docker run -d --name lbaw2425 -p 8001:80 gitlab.up.pt:5050/lbaw/lbaw2425/lbaw24125
```

After starting the image, the website can be accessed via http://localhost:8001.

A more detailed guide on how to run this project locally can be found [here](./src/README.md).

## Team

* Beatriz Ferreira, up202205612@up.pt
* Gabriel Lima, up202206693@up.pt
* Sara Cortez, up202205636@up.pt

***
GROUP24125, 09/10/2024
