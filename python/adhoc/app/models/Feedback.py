from orator import Model

class Feedback(Model):
    __table__ = 'feedback'
    __timestamps__ = False

    def validate(self):
        if "@" in self.email:
            return True
        return False

    def save(self, **kwargs):
        if self.validate():
            return super().save()
        return False




