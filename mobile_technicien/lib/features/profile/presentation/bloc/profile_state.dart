part of 'profile_bloc.dart';

sealed class ProfileState extends Equatable {
  const ProfileState();

  @override
  List<Object?> get props => [];
}

class ProfileLoading extends ProfileState {
  const ProfileLoading();
}

class ProfileError extends ProfileState {
  final String message;
  const ProfileError(this.message);

  @override
  List<Object?> get props => [message];
}

class ProfileLoaded extends ProfileState {
  final ProfileData data;
  const ProfileLoaded(this.data);

  @override
  List<Object?> get props => [data];
}
