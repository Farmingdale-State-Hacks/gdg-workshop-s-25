.PHONY: venv install run clean

venv:
	python -m venv venv

install:
	. venv/bin/activate && pip install -r requirements.txt

run:
	. venv/bin/activate && python app/main.py

clean:
	rm -rf venv
	find . -type d -name __pycache__ -exec rm -rf {} +
	find . -type f -name "*.pyc" -delete 