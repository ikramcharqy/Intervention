import 'package:equatable/equatable.dart';

class Technicien extends Equatable {
  final int id;
  final String name;
  final String? prenom;
  final String email;
  final List<String> roles;

  const Technicien({
    required this.id,
    required this.name,
    this.prenom,
    required this.email,
    required this.roles,
  });

  @override
  List<Object?> get props => [id, name, prenom, email, roles];
}
